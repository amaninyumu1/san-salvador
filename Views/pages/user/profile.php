<?php
$images = explode("AND", $params['user']->getPhoto());
?>

<div class="col-span-12 overflow-x-hidden  primary_bg">

    <div class="w-full flex justify-between lg:h-24 h-auto flex-col lg:flex-row p-2 primary_bg_ border-gray-800 border-b">
        <div class="flex lg:hidden my-2 justify-between">
            <h1 class="text-gray-200 font-semibold">USALVAGETRADE</h1>
             <button id="hamburger" class="w-8 h-8 rounded sm:hidden items-center flex justify-center border my-auto text-gray-800">
                <i class="fas fa-bars text-xl text-gray-200"></i>
            </button>
        </div>
        <nav id="mobile" class="hidden flex-col fixed w-screen h-screen z-1000 top-0 secondary_bg">
            <div class="flex justify-end w-11/12 mx-auto">
                <button id="times" class="w-8 h-8  sm:hidden flex justify-center items-center border rounded mt-4 text-white">
                    <i class="fas fa-times text-xl text-gray-200"></i>
                </button>
            </div>
            <ul class="flex w-full justify-evenly text-center  h-96 flex-col text-white">
            <li class="text-base"><span><a href="/user/dashboard">Dashboard</a></span></li>
                    <li class="text-base"><span><a href="/user/tree">Arbre</a></span></li>
                    <li class="text-base"><span><a href="/user/cashout">Retrait</a></span></li>
                    <li class="text-base"><span><a href="/">Acceuil</a></span></li>
                    <li class="text-base"><span><a href="/user/share/link">Partager</a></span></li>
                    <li class="hover:text-green-500 text-base"><a href="/user/me">Mon Compte</a></li>
                    <li class="hover:text-green-500 font-semibold text-base"><a href="/user/logout">Déconnexion</a></li>
            </ul>
            <p class="text-gray-400 w-full mx-auto text-center mt-36">&#169; USALVAGETRADE <span id="year"></span></p>
        </nav>
        <div id="user-identifiers" class="lg:w-3/12 w-full h-full flex ">
            <div class="h-16 w-16 overflow-hidden grid place-items-center border-gray-800 border rounded-full primary_bg">
                <img class="object-contain" src="/assets/img/<?=$images[0]?>" alt="<?=$_SESSION["users"]->getName()?>">
            </div>
            <div class="w-7/12 flex flex-col pl-5">
                <span class="text-gray-300 font-semibold text-base lg:text-lg"><?=$_SESSION["users"]->getName()?></span>
                <span class="text-gray-400 lg:text-base text-sm"><?=$_SESSION["users"]->getEmail()?></span>
                <span class="text-green-500 border border-green-500 rounded-full w-16 text-center p-0.5 text-xs">En ligne</span>
            </div>
            <div class="w-2/12 lg:hidden flex flex-col">
                <span class="bg-yellow-500 text-gray-900 place-items-center px-2 flex h-6 rounded-full">
                    <span class="text-xs font-semibold mr-1"><?= $params['user']->getPack()->getName() ?></span> <i class="fas text-xs fa-check-circle "></i>
                </span>
                <span class="flex items-center border-gray-800 border mt-3 rounded-full">
                    <span class="bg-gray-300 grid mr-4 text-gray-900 place-items-center w-6 h-6 rounded-full">
                        <i class="fas text-sm fa-dollar-sign "></i>
                    </span>
                    <span class="font-semibold text-gray-300 text-sm my-auto">
                        <?= $params['user']->getSold() ?>
                    </span>
                </span>
            </div>
        </div>
        <div class="w-2/12 lg:flex hidden items-center h-full">
            <span class="bg-gray-300 grid mr-4 ml-3 text-gray-900 place-items-center w-8 h-8 rounded-full">
                <i class="fas fa-dollar-sign "></i>
            </span>
            <span class="font-semibold text-gray-300 text-xl my-auto">
                <?= $params['user']->getSold() ?>
            </span>
        </div>
        <div class="w-2/12 lg:flex hidden items-center h-full">
            <span class="bg-yellow-500 text-gray-900 place-items-center px-2 flex h-12 rounded-full">
                <span class="text-base font-semibold mr-1"><?= $params['user']->getPack()->getName() ?></span> <i class="fas fa-check-circle "></i>
            </span>
        </div>
        <div class="w-2/12 lg:flex hidden items-center h-full">
            <span class="bg-gray-300 grid text-gray-900 place-items-center w-8 h-8 rounded-full">
                <i class="fas fa-calendar "></i>
            </span>
            <span class="text-gray-300 flex pl-2 flex-col my-auto">
                <span>Membre depuis </span>
                <span><?= $params['user']->getrecordDate()->format("F Y") ?></span>
            </span>
        </div>
        <div class="lg:w-3/12 w-full border-gray-800 lg:border lg:p-2 p-1 lg:mr-3 h-full rounded-xl">
            <div class="lg:flex hidden relative">
                <span class="w-3 h-3 animate-ping rounded-full absolute bg-green-400 opacity-75"></span>
                <span class="w-2 h-2  top-1 rounded-full absolute bg-green-500"></span>
                <span class="text-green-500 absolute left-10 -top-1 "> Evolution de votre compte</span>
            </div>
            <div class="w-full h-2 overflow-hidden lg:mt-8 mt-2 mr-3 border-green-500 border rounded">
                <div style="width: calc(<?= $params['user']->getBonusToPercent() ?>%)" class="h-full bg-green-500">

                </div>
            </div>
            <div class="text-gray-500 text-sm flex justify-between">
                <span><?= $params['user']->getBonusToPercent() * 3?>%</span>
                <span class="text-green-500">300%</span>
            </div>
        </div>
    </div>
    <div class="w-full mt-4 grid grid-cols-12">
        <div class="col-span-2 relative hidden lg:block ml-1 h-screen-customer rounded border border-gray-800 primary_bg_">
            <div data-path-user="/user/dashboard" class="flex p-2 my-2 from-green-500 to-gray-900 text-white transition-all duration-500   cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-school"></i></span>
                    <span class="w-10/12 mt-0.5">Dashboard</span>
                </div>
            </div>
            <div data-path-user="/user/tree" class="flex p-2 my-2 transition-all duration-500  text-gray-500 cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-tree"></i></span>
                    <span class="w-10/12 mt-0.5">Arbre</span>
                </div>
            </div>
            <div data-path-user="/user/me" class="flex p-2 my-2 transition-all duration-500  text-gray-500 cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-user"></i></span>
                    <span class="w-10/12 mt-0.5">Mon Compte</span>
                </div>
            </div>
            <div data-path-user="/user/cashout" class="flex p-2 my-2 transition-all duration-500  text-gray-500 cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-dollar-sign"></i></span>
                    <span class="w-10/12 mt-0.5">Retrait</span>
                </div>
            </div>
            <div data-path-user="/user/history" class="flex p-2 my-2 transition-all duration-500  text-gray-500 cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-history"></i></span>
                    <span class="w-10/12 mt-0.5">Historique de retrait</span>
                </div>
            </div>
            <div data-path-user="/user/share/link" class="flex p-2 my-2 transition-all duration-500  text-gray-500 cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-share"></i></span>
                    <span class="w-10/12 mt-0.5">Partager</span>
                </div>
            </div>
            <div data-path-user="/packages" class="flex p-2 my-2 transition-all duration-500  text-gray-500 cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-upload"></i></span>
                    <span class="w-10/12 mt-0.5">Remonter de pack</span>
                </div>
            </div>
            <div data-path-user="/" class="flex p-2 my-2 transition-all duration-500  text-gray-500 cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-arrow-left"></i></span>
                    <span class="w-10/12 mt-0.5">Retour à l'acceuil</span>
                </div>
            </div>
            <div data-path-user="/user/logout" class="flex p-2 my-2 transition-all duration-500  text-gray-500 cursor-pointer bg-gradient-to-r hover:from-green-500 hover:to-gray-900 hover:text-white">
                <div class="w-11/12 mx-auto flex ">
                    <span class="w-2/12"><i class="fas fa-power-off"></i></span>
                    <span class="w-10/12 mt-0.5">Déconnexion</span>
                </div>
            </div>
            <div class="absolute bottom-0 left-4 h-16 text-gray-500">
                <span class="text-center">Usalvagetrade &#169; <span id="year"></span></span>
            </div>
        </div>
        <div class="lg:col-span-10 col-span-12 h-screen-customer scroll lg:overflow-y-auto lg:overflow-x-hidden flex flex-col lg:p-3">
            <div class="grid lg:grid-cols-12 col-span-1 lg:space-x-2 p-2">

                <div class="lg:col-span-4 col-span-1 primary_bg_  p-4 mt-6 h-36 rounded-xl shadow">
                    <div class="flex">
                        <span class="text-blue-500 font-semibold"><i class="fas fa-comment-dollar"></i> MONTANT INVESTI</span>
                    </div>
                    <div class="w-full  rounded-full">
                        <div class="flex">
                            <span class="bg-blue-500 grid mt-3 mr-4 text-gray-900 place-items-center w-8 h-8 rounded-full">
                                <i class="fas fa-dollar-sign "></i>
                            </span>
                            <span class="font-semibold text-blue-500 mt-3 text-2xl my-auto">
                                <?= $params['user']->getCapital() ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-4 col-span-1 primary_bg_  p-4 mt-6 h-36 rounded-xl shadow">
                    <div class="flex">
                        <span class="text-yellow-500 font-semibold"><i class="fas fa-object-group    "></i> REVENU BINAIRE</span>
                    </div>
                    <div class="w-full  rounded-full">
                        <div class="flex">
                            <span class="bg-yellow-500 grid mt-3 mr-4 text-gray-900 place-items-center w-8 h-8 rounded-full">
                                <i class="fas fa-dollar-sign "></i>
                            </span>
                            <span class="font-semibold text-yellow-300 mt-3 text-2xl my-auto">
                                <?= $params['user']->getSoldBinary() ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-4 col-span-1 primary_bg_  p-4 mt-6 h-36 rounded-xl shadow">
                    <div class="flex">
                        <span class="text-pink-500 font-semibold"><i class="fas fa-users"></i> REVENU DIRECT</span>
                    </div>
                    <div class="w-full  rounded-full">
                        <div class="flex">
                            <span class="bg-pink-500 grid mt-3 mr-4 text-gray-900 place-items-center w-8 h-8 rounded-full">
                                <i class="fas fa-dollar-sign "></i>
                            </span>
                            <span class="font-semibold text-pink-500 mt-3 text-2xl my-auto">
                                <?= $params['user']->getSoldParainage() ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid lg:grid-cols-12 grid-cols-1 lg:space-x-2 p-2">
                <div class="lg:col-span-4 col-span-1 primary_bg_  p-4 mt-6 h-36 rounded-xl shadow">
                    <div class="flex">
                        <span class="text-blue-500 font-semibold"><i class="fas fa-comment-dollar"></i> BONUS JOURNALIER</span>
                    </div>
                    <div class="w-full  rounded-full">
                        <div class="flex">
                            <span class="bg-blue-500 grid mt-3 mr-4 text-gray-900 place-items-center w-8 h-8 rounded-full">
                                <i class="fas fa-dollar-sign "></i>
                            </span>
                            <span class="font-semibold text-blue-500 mt-3 text-2xl my-auto">
                                <?= "{$params['user']->getSoldResturn()}   "  . " Taux de : " . $params['user']->getPack()->getAcurracy() ?> % / Jour
                            </span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 col-span-1 primary_bg_  p-4 mt-6 h-36 rounded-xl shadow">
                    <div class="flex">
                        <span class="text-yellow-500 font-semibold"><i class="fas fa-comment-dollar"></i> CAPITAUX INVESTI A GAUCHE</span>
                    </div>
                    <div class="w-full  rounded-full">
                        <div class="flex">
                            <span class="bg-yellow-500 grid mt-3 mr-4 text-gray-900 place-items-center w-8 h-8 rounded-full">
                                <i class="fas fa-dollar-sign "></i>
                            </span>
                            <span class="font-semibold text-yellow-300 mt-3 text-2xl my-auto">
                                <?= $params['gauche'] ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 col-span-1 primary_bg_  p-4 mt-6 h-36 rounded-xl shadow">
                    <div class="flex">
                        <span class="text-pink-500 font-semibold"><i class="fas fa-comment-dollar"></i> CAPITAUX INVESTI A DROITE</span>
                    </div>
                    <div class="w-full  rounded-full">
                        <div class="flex">
                            <span class="bg-pink-500 grid mt-3 mr-4 text-gray-900 place-items-center w-8 h-8 rounded-full">
                                <i class="fas fa-dollar-sign "></i>
                            </span>
                            <span class="font-semibold text-pink-500 mt-3 text-2xl my-auto">
                                <?= $params['droite'] ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>