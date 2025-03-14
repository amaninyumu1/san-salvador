<?php

namespace Root\App\Controllers;

use ArrayObject;
use Root\App\Controllers\Validators\AdiminValidator;
use Root\App\Controllers\Validators\UserValidator;
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

    public function allUsersHasValidateInscription($limit = null, $offset = 0)
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

    /**
     * Pour controller la rendu du view
     *
     * @return void
     */
    public function control()
    {
        if (!$this->userObject()->hasInscription()) {
            return $this->view("pages.user.hasNotSubscribedYet", "layout_", ['user' => $_SESSION[self::SESSION_USERS]]);
        }
        if ($this->existValidateInscription() && !$this->inscriptionModel->checkValidated($_SESSION[self::SESSION_USERS]->getId())) {
            return $this->view("pages.user.awaitUserPackValidation", "layout_");
        }
    }

    /**
     * Count Inscription valide et not valide
     *
     * @param boolean $valiadte
     * @return mixed
     */
    public function countInscription(bool $valiadte = true)
    {
        if ($this->inscriptionModel->checkValidated()) {
            //var_dump($this->inscriptionModel->countValidate($valiadte));exit;
            return $this->inscriptionModel->countValidate($valiadte);
        }
        return 0;
    }
    /**
     * All inscription not active
     *
     * @return  array
     */
    public function allNonValidateInscription(?int $limit = null, ?int $offset = 0)
    {
        $return = array();
        if ($this->inscriptionModel->checkAwait()) {
            $allValidate = $this->inscriptionModel->findAwait($limit, $offset);
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
                $inscription->setUser($this->userModel->findById($idUser));

                //les informations du mail
                $name = $inscription->getUser()->getName();
                $mail = $inscription->getUser()->getEmail();
                $montant = $inscription->getAmount();
                $this->envoieMail($mail, "Activation de l'inscription", "pages/mail/suscrubeMail", ['nom' => $name, 'montant' => $montant]);
                $this->inscriptionModel->validate($idInscription, $idAdmin);
                header("location:" . $_SERVER['HTTP_REFERER']);
            } else {
                Controller::redirect('admin/login');
            }
        } else {
            return $this->view("pages.static.404");
        }
    }

    /**
     * Annulation de l'inscription
     *
     * @return void
     */
    public function annulerInscription()
    {
        $idInscription = $_GET['inscription'];
        if ($this->inscriptionModel->checkById($idInscription)) {
            $this->inscriptionModel->delete($idInscription);
            header("location:" . $_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * touts les retrait en attente de validation
     * @return array
     */
    public function viewAllCashOutNotValide(?int $limit = null, ?int $offset = 0)
    {
        $return = array();
        if ($this->cashOutModel->checkValidated(null, false)) {
            $allNotActive = $this->cashOutModel->findValidated(false, null, $limit, $offset);
            foreach ($allNotActive as $nonActive) {
                $nonActive->setUser($this->userModel->findById($nonActive->getUser()->getId()));
                $return[] = $nonActive;
            }
            return $return;
        }
        return $return;
    }
    /**
     * touts les retrait deja valider
     * @return array
     */
    public function viewAllCashOutValidate(?int $limit = null, ?int $offset = 0)
    {
        $return = array();
        if ($this->cashOutModel->checkValidated(null, true)) {
            $allNotActive = $this->cashOutModel->findValidated(true, null, $limit, $offset);
            foreach ($allNotActive as $nonActive) {
                $nonActive->setUser($this->userModel->findById($nonActive->getUser()->getId()));
                $return[] = $nonActive;
            }
            return $return;
        }
        return $return;
    }

    public function viewAllHistoryCashOutForUser(?bool $validate = false)
    {
        $return = array();
        $idUser = $_SESSION[self::SESSION_USERS]->getId();
        if ($this->cashOutModel->checkByUserWithStatus($idUser, $validate)) {
            $cashOuts = $this->cashOutModel->findByUserWithStatus($idUser, $validate);
            foreach ($cashOuts as $cashOut) {
                $cashOut->setUser($this->userModel->findById($idUser));
                $return[] = $cashOut;
            }
            return $return;
        }
        return $return;
    }

    /**
     *Function pour compte les cashOut
     *
     * @param boolean $validated
     * @return mixed
     */
    public function countCashOut(bool $validated = false)
    {
        if ($this->cashOutModel->checkValidated(null, $validated)) {
            $cashOut = $this->cashOutModel->countValidated(null, $validated);
            return $cashOut;
        }
        return 0;
    }

    /**
     * Pour annuler une demande de retrait
     *
     * @return void
     */
    public function cancelCashOut()
    {
        $idCashOut = $_GET['cashout'];
        if ($this->cashOutModel->checkById($idCashOut)) {
            if ($this->cashOutModel->checkValidated()) {
                $this->cashOutModel->delete($idCashOut);
            }
        } else {
            return $this->view("pages.static.404");
        }
    }

    /**
     * Methode pour retourner la liste des users ayant deja un souscrit a un pack
     * @param integer|null $limit
     * @param integer|null $offset
     * @return array
     */
    public function usersValidate(?int $limit = null, ?int $offset = 0)
    {
        if ($this->userModel->checkCertifieds()) {
            $users = $this->userModel->findCertifieds($limit, $offset);
            return $users;
        }
    }

    /**
     * Methode pour compter des users ayant deja un souscrit a un pack
     * @return int 
     */
    public function countUsersValidate()
    {
        if ($this->userModel->checkCertifieds()) {
            return $this->userModel->countCertifieds();
        }
        return 0;
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
        return 0;
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
        return 0;
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
        return 0;
    }

    /**
     * Calcul des surplus du systeme
     *
     * @return mixed
     */
    public function allSurplus()
    {
        $totalSurplus = 0;
        $surplusBinaryReturn = array();
        $surplusParainage = array();
        $surplusReturnInvest = array();
        if ($this->parainageModel->checkAll()) {
            /**
             * @var Parainage
             */
            $parainages = $this->parainageModel->findAll();
            foreach ($parainages as $parainage) {
                $parainage->setUser($this->userModel->findById($parainage->getUser()->getId()));
                $surplus = $parainage->getSurplus();
                $surplusParainage[] = $surplus;
            }
        }

        if ($this->returnInvestModel->checkAll()) {
            /**
             * @var ReturnInvest
             */
            $returnInvests = $this->returnInvestModel->findAll();
            foreach ($returnInvests as $returnInvest) {
                $returnInvest->setUser($this->userModel->findById($returnInvest->getUser()->getId()));
                $surplusInvest = $returnInvest->getSurplus();
                $surplusReturnInvest[] = $surplusInvest;
            }
        }

        if ($this->binaryModel->checkAll()) {
            /**
             * @var Binary
             */
            $binarys = $this->binaryModel->findAll();
            foreach ($binarys as $binary) {
                $binary->setUser($this->userModel->findById($binary->getUser()->getId()));
                $surplusBinary = $binary->getSurplus();
                $surplusBinaryReturn[] = $surplusBinary;
            }
        }
        $totalSurplus = array_sum($surplusParainage) + array_sum($surplusReturnInvest) + array_sum($surplusBinaryReturn);
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
     * Function pour calculer le montant total des cashOuts en attente deja valider
     * @return mixed
     */
    public function amountAllCashOutValide()
    {
        $return = array();
        $cashOuts = $this->viewAllCashOutValidate();
        foreach ($cashOuts as $cashOut) {
            $montant = $cashOut->getAmount();
            $return[] = $montant;
        }
        return array_sum($return);
    }

    /**
     * Methode pour retourner le montant total deja investi dans le systeme
     *
     * @return float|int
     */
    public function totalAmountInvested()
    {
        $return = array();
        $allUsersHasPack = $this->allUsersHasValidateInscription();
        foreach ($allUsersHasPack as $inscription) {
            if (!$inscription->getUser()->getParent() == null && !$inscription->getUser()->getSponsor() == null) {
                $return[] = $inscription->getAmount();
            }
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

    /**
     * Pour envoyer les mails d'actiavation du compte
     * @param string $to. Le destinataire du mail
     * @param mixed $lien. Le lien d'activation de compte
     */
    public function envoieMail($to,  string $sujet = null, $path, array $params = null)
    {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        // En-têtes additionnels
        $headers[] = "From: \"UsalvageTrade\"<contact@usalvagetrade.com>";
        $headers[] = 'Repay-To: support@usalvagetrade.com';
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $sujet = $sujet;
        ob_start();
        require VIEWS . $path . '.php';
        if ($params) {
            $params = extract($params);
        }
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

    /**
     * Function pour afficher les donnees subdiviser sous forme des pages 
     * @param integer $totalCount
     * @param integer $page
     * @param integer $nombre_element_par_page
     * @return array($debut[0],$nombre_pages[1])
     */
    public static function drowData($totalCount, $page, $nombre_element_par_page = 5)
    {
        $nombre_pages = ceil($totalCount / $nombre_element_par_page);
        $debut = ($page - 1) * $nombre_element_par_page;
        return array($debut, $nombre_pages);
    }
}
