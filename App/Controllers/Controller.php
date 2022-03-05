<?php

namespace Root\App\Controllers;

use ArrayObject;
use Root\App\Models\BinaryModel;
use Root\App\Models\CashOutModel;
use Root\App\Models\InscriptionModel;
use Root\App\Models\ModelFactory;
use Root\App\Models\Objects\Binary;
use Root\App\Models\Objects\CashOut;
use Root\App\Models\Objects\Inscription;
use Root\App\Models\Objects\Parainage;
use Root\App\Models\Objects\ReturnInvest;
use Root\App\Models\Objects\User;
use Root\App\Models\PackModel;
use Root\App\Models\ParainageModel;
use Root\App\Models\ReturnInvestModel;
use Root\App\Models\UserModel;
use Root\Core\GenerateId;

class Controller
{
    const SESSION_ADMIN = 'admin';
    const SESSION_USERS = 'users';
    /**
     * Inscrption Model
     *
     * @var InscriptionModel
     */
    private $inscriptionModel;

    /**
     * Pack Model
     *
     * @var PackModel
     */
    private $packModel;

    /**
     * Parainage Model
     *
     * @var ParainageModel
     */
    private $parainageModel;

    /**
     * Binary Model
     *
     * @var BinaryModel
     */
    private $binaryModel;

    /**
     * ReturnInvest Model
     *
     * @var ReturnInvestModel
     */
    private $returnInvestModel;

    /**
     * User Model
     *
     * @var UserModel
     */
    private $userModel;

    /**
     * CashOut Model
     *
     * @var CashOutModel
     */
    private $cashOutModel;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->inscriptionModel = ModelFactory::getInstance()->getModel("Inscription");
        $this->packModel = ModelFactory::getInstance()->getModel("Pack");
        $this->parainageModel = ModelFactory::getInstance()->getModel("Parainage");
        $this->binaryModel = ModelFactory::getInstance()->getModel("Binary");
        $this->returnInvestModel = ModelFactory::getInstance()->getModel("ReturnInvest");
        $this->userModel = ModelFactory::getInstance()->getModel("User");
        $this->cashOutModel = ModelFactory::getInstance()->getModel("CashOut");
    }

    /**
     * La methode pour retourner le tableau des utilisateurs
     *
     * @return array
     */
    public function getAllPck()
    {
        return $this->packModel->findAll();
    }

    /**
     * La methode qui retourner l'objet utilisateur
     *
     * @return User
     */
    public function userObject()
    {
        if ($this->sessionExist($_SESSION[self::SESSION_USERS])) {
            $id = $_SESSION[self::SESSION_USERS]->getId();
            return $this->userModel->load($id);
        }
    }

    /**
     * La fonction pour retourner les operation des plusieurs utilisateurs
     *
     * @return User[]
     */
    public function allUsersObjects()
    {
        return $this->userModel->loadAll();
    }

    /**
     * Undocumented function
     *
     * @return User
     */
    public function allUsers()
    {
        if ($this->sessionExist($_SESSION[self::SESSION_USERS])) {
            $id = $_SESSION[self::SESSION_USERS]->getId();
            /**
             * @var User
             */
            $users = $this->userModel->findById($id);
            $downlines = $this->userModel->loadDownlineLeftRightSides($id);
            $users->setSides($downlines);
            return $users;
        }
    }
    /**
     * Methode pour verifier s'il y'a un pack en attende de validation
     *
     * @return bool
     */
    public function existValidateInscription()
    {
        return $this->inscriptionModel->checkAwait($_SESSION[self::SESSION_USERS]->getId());
    }

    //Methode pour verifier si l'utilisateur au moins une inscription active
    public function existOneValidateInscription()
    {
        return $this->inscriptionModel->hasPack($_SESSION[self::SESSION_USERS]->getId());
    }

    public function allUsersHasValidateInscription($limit = 0, $offset = 0)
    {
        $return = array();
        if ($this->inscriptionModel->checkValidated()) {
            $allUsersPacks = $this->inscriptionModel->findValidated($limit, $offset);
            foreach ($allUsersPacks as $allUsers) {
                $allUsers->setUser($this->userModel->findById($allUsers->getUser()->getId()));

                $return[] = $allUsers;
            }
            return  $return;
        }
        return $return;
    }

    public function countValidateInscription()
    {
        if ($this->inscriptionModel->checkValidated()) {
            return $this->inscriptionModel->countValidate();
        }
        return 0;
    }
    /**
     * All inscription not active
     *
     * @return  array
     */
    public function allNonValidateInscription()
    {
        $return = array();
        if ($this->inscriptionModel->checkAwait()) {
            $allValidate = $this->inscriptionModel->findAwait();
            //var_dump($allValidate); exit();
            foreach ($allValidate as $validate) {
                $validate->setUser($this->userModel->findById($validate->getUser()->getId()));
                $return[] = $validate;
            }
        }
        return $return;
    }


    /**
     * Pour valider une inscription
     *
     * @return void
     */
    public function activeInscription()
    {
        $idInscription = $_GET['inscription'];
        $idAdmin = $_SESSION[self::SESSION_ADMIN]->getId();
        $idUser = $_GET['user'];
        /**
         * @var Inscription
         */
        $inscription = $this->inscriptionModel->findById($idInscription);
        if ($this->inscriptionModel->checkById($idInscription)) {
            if (!$inscription->isValidate()) {
                $this->inscriptionModel->validate($idInscription, $idAdmin);
            } else {
                Controller::redirect('admin/login');
            }
        } else {
            return $this->view("pages.static.404");
        }
    }

    /**
     * touts les retrait en attente de validation
     * @return array
     */
    public function viewAllCashOutNotValide()
    {
        $return = array();
        if ($this->cashOutModel->checkValidated()) {
            $allNotActive = $this->cashOutModel->findValidated();
            foreach ($allNotActive as $nonActive) {
                $nonActive->setUser($this->userModel->findById($nonActive->getUser()->getId()));
                $return[] = $nonActive;
            }
        }
        return $return;
    }
    /**
     * touts les retrait deja valider
     * @return array
     */
    public function viewAllCashOutValidate()
    {
        $return = array();
        if ($this->cashOutModel->checkValidated()) {
            $allNotActive = $this->cashOutModel->findValidated();
            foreach ($allNotActive as $nonActive) {
                $nonActive->setUser($this->userModel->findById($nonActive->getUser()->getId()));
                $return[] = $nonActive;
            }
        }
        return $return;
    }

    /**
     * Activation du cashOut
     *
     * @return void
     */
    public function activeCashOut()
    {
        $idCashOut = $_GET['cashout'];
        $idAdmin = $_SESSION[self::SESSION_ADMIN]->getId();
        $idUser = $_GET['user'];
        //var_dump($this->cashOutModel->checkById($idCashOut)); exit();
        if ($this->cashOutModel->checkById($idCashOut)) {
            if ($this->cashOutModel->checkValidated($idCashOut)) {
                $this->cashOutModel->validate($idCashOut, $idAdmin);
            } else {
                Controller::redirect('admin/login');
            }
        } else {
            return $this->view("pages.static.404");
        }
    }



    /**
     * Fonction pour retourner la sommes des tous les montants binaires du systeme
     *
     * @return mixed
     */
    public function allBinary()
    {
        $return = array();

        if ($this->binaryModel->checkAll()) {

            /**
             * @var Binary
             */
            $binarys = $this->binaryModel->findAll();
            foreach ($binarys as $binary) {
                $binary->setUser($this->userModel->findById($binary->getUser()->getId()));
                $montant = $binary->getAmount();
                $return[] = (int) $montant;
            }
            return array_sum($return);
        }
        return $return;
    }

    /**
     * Fonction pour retourner le montant total des bonus journaliers du systeme
     *
     * @return mixed
     */
    public function allReturnInvest()
    {
        $return = array();

        if ($this->returnInvestModel->checkAll()) {
            /**
             * @var ReturnInvest
             */
            $invests = $this->returnInvestModel->findAll();
            foreach ($invests as $invest) {
                $invest->setUser($this->userModel->findById($invest->getUser()->getId()));
                $return[] = $invest->getAmount();
            }
            return round(array_sum($return), 2);
        }
        return $return;
    }

    /**
     * la fonction pour me retourner touts les montant binaire du systeme
     *
     * @return mixed
     */
    public function allParainage()
    {
        $return = array();

        if ($this->parainageModel->checkAll()) {
            /**
             * @var Parainage
             */
            $parainages = $this->parainageModel->findAll();
            foreach ($parainages as $parainage) {
                $parainage->setUser($this->userModel->findById($parainage->getUser()->getId()));
                $montant = $parainage->getAmount();
                $return[] = $montant;
            }
            return array_sum($return);
        }
        return $return;
    }

    /**
     * Calcul des surplus du systeme
     *
     * @return mixed
     */
    public function allSurplus()
    {
        $totalSurplus = 0;
        $surplusBinary = array();
        $surplusParainage = array();
        $surplusReturnInvest = array();
        if ($this->parainageModel->checkAll() || $this->returnInvestModel->checkAll() || $this->binaryModel->checkAll()) {
            /**
             * @var Parainage
             */
            $parainages = $this->parainageModel->findAll();
            foreach ($parainages as $parainage) {
                $parainage->setUser($this->userModel->findById($parainage->getUser()->getId()));
                $surplus = $parainage->getSurplus();
                $surplusParainage[] = $surplus;
            }

            /**
             * @var ReturnInvest
             */
            $returnInvests = $this->returnInvestModel->findAll();
            foreach ($returnInvests as $returnInvest) {
                $returnInvest->setUser($this->userModel->findById($parainage->getUser()->getId()));
                $surplusInvest = $returnInvest->getSurplus();
                $surplusReturnInvest[] = $surplusInvest;
            }

            /**
             * @var Binary
             */
            $binarys = $this->binaryModel->findAll();
            foreach ($binarys as $binary) {
                $binary->setUser($this->userModel->findById($parainage->getUser()->getId()));
                $surplusBinary = $binary->getSurplus();
                $surplusBinaryReturn[] = $surplusBinary;
            }
            $totalSurplus = array_sum($surplusParainage) + array_sum($surplusReturnInvest) + array_sum($surplusBinaryReturn);
            return $totalSurplus;
        }
        return $totalSurplus;
    }

    /**
     * Function pour calculer le montant total des cashOuts en attente de validation
     * @return mixed
     */
    public function amountAllCashOutNotValide()
    {
        $return = array();
        $cashOuts = $this->viewAllCashOutNotValide();
        foreach ($cashOuts as $cashOut) {
            $montant = $cashOut->getAmount();
            $return[] = $montant;
        }
        return array_sum($return);
    }
    /**
     * Destroy all session
     *
     * @return void
     */
    public static function destroyAllSession()
    {
        if (isset($_SESSION)) {
            $_SESSION = array();
            session_destroy();
        }
    }
    /**
     * Pour rendre les views dans l'application
     * @param string $path. Le lien ou se trouve la vue a rendre dans le dossier Views
     * @param string $template. Le Container de notre views
     * @param array|null $params. Les donnees a transmettre a la vue
     *  @return void
     */
    public function view(string $path, string $template = 'layouts', array $params = null)
    {
        ob_start();
        $path = str_replace('.', DIRECTORY_SEPARATOR, $path);
        require VIEWS . $path . '.php';
        if ($params) {
            $params = extract($params);
        }
        $content = ob_get_clean();
        require VIEWS . $template . '.php';
    }
    // /**
    //  * Pour genener un melnge de chaine de caractere
    //  *
    //  * @param integer $length. La longueur de la chaine de caractere a genener
    //  * @param string $carateres. Les caracteres a melanger
    //  * @return string
    //  */
    // public static function generate(int $length, string $carateres)
    // {
    //     return substr(str_shuffle(str_repeat($carateres, $length)), 0, $length);
    // }

    /**
     * Pour envoyer les mails d'actiavation du compte
     * @param string $to. Le destinataire du mail
     * @param mixed $lien. Le lien d'activation de compte
     */
    public function envoieMail($to, string $lien, string $sujet = null, $path, $nom)
    {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        // En-têtes additionnels
        $headers[] = 'From: contact@usalvagetrade.com';
        $headers[] = 'Repay-To: contact@usalvagetrade.com';
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $sujet = $sujet;
        ob_start();
        require VIEWS . $path . '.php';
        $message = ob_get_clean();

        return mail($to, $sujet, $message, implode("\r\n", $headers));
    }
    /**
     * La fonction pour redimentionner une image
     *
     * @param mixed $source. La source de l'image a redimensionner
     * @param mixed $destination. La destination du fichier redimensionner
     * @param mixed $name. Le nom du fichier redimensionner
     * @param mixed $width.La largeur du fichier redimensionner
     * @param mixed $height. La hateur du fichier redimensionner
     * @return void
     */
    public function convertImage($source, $destination, $name, $width, $height)
    {

        //[0]=>width et [1]=>height
        $imageSize = getimagesize($source);
        $extension = strrchr($imageSize['mime'], "/");
        if ($extension == '/jpeg') {
            $imageRessource = imagecreatefromjpeg($source);
            $imageFinale = imagecreatetruecolor($width, $height);
            $final = imagecopyresampled($imageFinale, $imageRessource, 0, 0, 0, 0, $width, $height, $imageSize[0], $imageSize[1]);
            imagejpeg($imageFinale, $destination . "$name.jpg", 100);
            return $destination . "$name.jpg";
        } elseif ($extension == '/png') {
            $imageRessource = imagecreatefrompng($source);
            $imageFinale = imagecreatetruecolor($width, $height);
            $final = imagecopyresampled($imageFinale, $imageRessource, 0, 0, 0, 0, $width, $height, $imageSize[0], $imageSize[1]);
            imagepng($imageFinale, $destination . "$name.png", 9);
            return $destination . "$name.png";
        }
    }
    /**
     * La fonction pour cree un repertoire dans le dossier assets/img/directory
     * @param mixed $directory. Le chemin de le nom du dossier a cree
     */
    public function createFolder($directory)
    {
        $path = RACINE . $directory;
        while (!is_dir($directory)) {
            mkdir($path);
            return $path . DIRECTORY_SEPARATOR;
        }
        return false;
    }
    /**
     * Undocumented function
     *
     * @param mixed $nom. Le du champs du type file dans le formulaire
     */
    public function addImage($nom)
    {
        $image = $_FILES[$nom]['name'];
        $temporaire = $_FILES[$nom]['tmp_name'];
        $directory = $this->createFolder(GenerateId::generate(20, '123450ABCDEabcde'));
        $destination = $directory . $image;
        if (move_uploaded_file($temporaire, $destination)) {
            $imgOrginal = $destination;
            $imgRedi = $this->convertImage($imgOrginal, $directory, 'x320', 96, 96);
            //chemin a enreistre dans la base des donnees
            $folder = str_replace(RACINE, "", $imgOrginal . ' AND ' . $imgRedi);
            return $folder;
        }
    }
    /**
     * Undocumented function
     *
     * @param array $errors. Le tableau des erreurs
     * @param string $keys. La
     * @return void
     */
    public static function errorsViews(array $errors, string $keys)
    {
        if ((isset($errors) && !empty($errors) && key_exists($keys, $errors))) {
            foreach ($errors as $keys => $value) {
                return $value;
            }
        }
    }

    /**
     * Pour la redirection automatique
     *
     * @param mixed $chemin
     * @return void
     */
    public static function redirect($chemin)
    {
        header('Location:' . $chemin);
    }
    /**
     * Verifie si la session existe deja
     *
     * @param mixed $session
     * @return true
     */
    public static function sessionExist($session)
    {
        if (isset($session) && !empty($session)) {
            return true;
        }
    }

    /**
     * Pour l'envoie du mail avec success
     *
     * @return void
     */
    public function mailSendSuccess()
    {
        return $this->view('pages.static.mail_sent_success', 'layout_', ['mail' => $_SESSION['mail']]);
    }
}
