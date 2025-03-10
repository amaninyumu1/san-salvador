"use strict";
var $;
(() => {
    const handler = document.querySelector("#menuHandler");
    const menu = document.querySelector("#menu");
    handler &&
        handler.addEventListener("click", () => {
            $("#menuContainer").css("padding", "15px");
            $(menu).slideToggle();
        });
})();
/**
 * All these lines bellow concern menu humberger
 */
const toggleButton = document.getElementById("toggle-button");
if (toggleButton) {
    const iconButton = toggleButton.querySelector(".fas");
    const navbar = document.getElementById("navbar");
    toggleButton.addEventListener("click", () => {
        navbar && navbar.classList.toggle("hidden");
        if (iconButton)
            if (iconButton.classList.contains("fa-bars")) {
                iconButton.classList.remove("fa-bars");
                iconButton.classList.add("fa-times");
            }
            else {
                iconButton.classList.remove("fa-times");
                iconButton.classList.add("fa-bars");
            }
    });
}
//End of menu humberger code
$(document).ready(() => {
    $("#hamburger").on("click", () => {
        $("#other").slideUp("slow");
        $("#mobile").slideDown("slow");
    });
    $("#times").on("click", () => {
        $("#mobile").slideUp("slow");
        $("#other").slideDown("slow");
    });
    $("#year").text(new Date().getFullYear().toString());
});
const imageUpload = document.querySelector("#image");
$(".hide-b4-save").hide();
/**
 * Initializing cropper class
 */
var Cropper;
const cropper = new Cropper({
    width: 320,
    height: 320,
    onChange: function () {
        const image = this.getCroppedImage();
        const file = dataURLtoFile(image, "user");
        if (imageUpload && file && imageUpload.files) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            imageUpload.files = dataTransfer.files;
        }
    },
});
/**
 * Covert base64 data image to Javascript object File
 * @param dataUrl
 * @param filename
 * @returns {File | undefined}
 */
function dataURLtoFile(dataUrl, filename) {
    let array = dataUrl.split(",");
    if (array !== null) {
        const matchExtension = array[0].match(/data:(.*);/);
        const matchMimeType = array[0].match(/:(.*?);/);
        let extension;
        let mime;
        if (matchExtension !== null) {
            extension = matchExtension[1].split("/")[1];
            if (matchMimeType !== null) {
                mime = matchMimeType[1];
                let bstr = atob(array[1]);
                let n = bstr.length;
                let u8array = new Uint8Array(n);
                while (n--) {
                    u8array[n] = bstr.charCodeAt(n);
                }
                return new File([u8array], filename + "." + extension, { type: mime });
            }
        }
    }
}
const imageUploader = document.querySelector("#imageToCrop");
imageUploader &&
    imageUploader.addEventListener("change", (evt) => {
        if (evt.target !== null) {
            const target = evt.target;
            const image = target.files[0];
            if (image) {
                const fileReader = new FileReader();
                fileReader.onload = function () {
                    cropper.loadImage(fileReader.result).then(() => { });
                    $("#crop").slideDown();
                    $("#crop").removeClass(".hidden");
                    $(".hide-b4-save").slideDown();
                };
                fileReader.readAsDataURL(image);
            }
        }
    });
document.querySelector("#crop") && cropper.render("#crop");
//Add user interactions stars here
const formStepsButtons = document.querySelectorAll(".form-user-btn");
formStepsButtons &&
    formStepsButtons.forEach((button) => {
        const buttonName = button.name;
        $(button).on("click", function (e) {
            e.preventDefault();
            useFormButtons(buttonName);
        });
        const cameraBtn = document.querySelector("#camera");
        if (cameraBtn && imageUploader)
            cameraBtn.addEventListener("click", (e) => {
                e.preventDefault();
                imageUploader.click();
                e.stopImmediatePropagation();
            });
        function useFormButtons(name) {
            switch (name) {
                case "2":
                    $(".form-1").slideUp();
                    $("#register-title").text("Ajouter votre photo en cliquant sur le bouton ci-bas");
                    $("#userIcon").slideUp();
                    $(".form-2").slideDown();
                    break;
                case "-2":
                    $(".form-1").slideDown();
                    $(".form-2").slideUp();
                    break;
            }
        }
    });
const userMenus = document.querySelectorAll("[data-path-user]");
userMenus.forEach((menu) => {
    menu.addEventListener("click", function () {
        const path = menu.getAttribute("data-path-user");
        if (path) {
            window.location.pathname = path;
        }
    });
});
/**
 * Pairing sides
 */
const paringSides = document.querySelector("#pairing-sides");
const sides = paringSides?.querySelectorAll("[data-side]");
sides?.forEach((side) => {
    side.addEventListener("click", (e) => {
        const sideId = side.getAttribute("data-side");
        setActiveSide(side);
    });
});
function setActiveSide(side) {
    $(side).attr("class", "flex border border-gray-400 cursor-pointer bg-green-500 text-gray-900 rounded h-12 p-1 items-center w-4/12 justify-between");
    $(side)
        .siblings()
        .attr("class", "flex border border-gray-400 cursor-pointer text-gray-300 rounded h-12 p-1 items-center w-4/12 justify-between");
    const sideCircle = side?.querySelector("span:nth-child(2)");
    $($(side).siblings().children()[1]).html("<i></i>");
    $(sideCircle).attr("class", "h-7 w-7 rounded-full border border-gray-900 grid place-items-center bg-gray-900");
    $(sideCircle).html("<i class='fas fa-check-circle text-green-500'></i>");
    $("#valueToCopy").text(`https://usalvagetrade.com/register-${$(side).data("side")}-${$("#valueToCopy").data("parent")}`);
}
/**
 * Helps to switch Jquery display for many elements
 * @param {Array<HTMLElement>} elements elements that will switch to another display
 * @param {displayType}display  display to give to the elements
 * @param {animationType} animation  display to give to the elements
 */
function displaySwitcher(elements, display, animation) {
    elements.forEach((elt) => {
        if (display === "hide" && animation === "slide") {
            $(elt).slideUp();
        }
        else if (display === "show" && animation === "slide") {
            $(elt).slideDown();
        }
    });
}
/**
 * Transactions buttons don't have nothing to do with admin transactions cheking
 * We are using these buttons to just switch between transaction sources
 * All these three buttons are used to just switch them
 */
const source = document.querySelector("#source");
const transactionBtns = document.querySelectorAll(".transaction-btn");
const defaultTransactionData = document.querySelector("#defaultTransactionData");
let AMTransactionData = document.querySelector("#AMTransactionData");
let MPSTransactionData = document.querySelector("#MPSTransactionData");
let BTCTransactionData = document.querySelector("#BTCTransactionData");
transactionBtns.forEach((btn) => {
    const dataTransType = btn.getAttribute("data-trans-type");
    $(btn).on("click", (e) => {
        e.preventDefault();
        /**
         * There's still some issues about the design and css logic for the buttons
         * About functionnality the are working perfectly
         */
        let activeBtnClass = "w-4/12 transaction-btn hover:bg-blue-800 bg-blue-600 text-white hover:text-white rounded-l transition-all duration-150 cursor-pointer justify-center font-semibold text-center flex items-center";
        if (dataTransType === "btc") {
            $("#MPSAndAMTransactionData").slideUp();
            $("#btcGraph").slideUp();
            $(source).val("BTC");
            AMTransactionData &&
                MPSTransactionData &&
                displaySwitcher([MPSTransactionData, AMTransactionData], "hide", "slide");
            BTCTransactionData &&
                displaySwitcher([BTCTransactionData], "show", "slide");
        }
        else if (dataTransType === "am") {
            $("#MPSAndAMTransactionData").slideDown();
            $("#btcGraph").slideUp();
            $(source).val("AirtelMoney");
            activeBtnClass =
                "w-4/12 transaction-btn hover:bg-blue-800 bg-blue-600 text-white hover:text-white  transition-all duration-150 cursor-pointer justify-center font-semibold text-center flex items-center";
            BTCTransactionData &&
                MPSTransactionData &&
                displaySwitcher([BTCTransactionData, MPSTransactionData], "hide", "slide");
            AMTransactionData &&
                displaySwitcher([AMTransactionData], "show", "slide");
        }
        else if (dataTransType === "mps") {
            $("#MPSAndAMTransactionData").slideDown();
            $("#btcGraph").slideUp();
            $(source).val("M-Pesa");
            activeBtnClass =
                "w-4/12 transaction-btn hover:bg-blue-800 bg-blue-600 text-white hover:text-white rounded-r  transition-all duration-150 cursor-pointer justify-center font-semibold text-center flex items-center";
            BTCTransactionData &&
                AMTransactionData &&
                displaySwitcher([BTCTransactionData, AMTransactionData], "hide", "slide");
            MPSTransactionData &&
                displaySwitcher([MPSTransactionData], "show", "slide");
        }
        defaultTransactionData &&
            displaySwitcher([defaultTransactionData], "hide", "slide");
        $(btn)
            .attr("class", activeBtnClass)
            .siblings()
            .attr("class", "w-4/12 transaction-btn hover:bg-blue-600 hover:text-white transition-all duration-150 cursor-pointer justify-center font-semibold text-center flex items-center text-gray-300");
    });
});
function menuHighLighter() {
    const knownPaths = [
        "/",
        "/packages",
        "/help",
        "/services",
        "/register",
        "/login",
        "/reset-password",
        "/contact",
        "/about",
        "/security",
        "/terms",
        "/user/pack/subscribe",
        "/user/share/link",
        "/user/dashboard",
        "/user/history",
        "/user/tree",
        "/user/me",
        "/user/cashout",
    ];
    const path = window.location.pathname;
    const menus = document.querySelectorAll("#defaultMenu li span a");
    menus.forEach((menu) => {
        const menuPath = menu.getAttribute("href");
        if (menuPath == path) {
            $(menu).attr("class", "_green_text font-semibold");
        }
    });
    if (knownPaths.indexOf(path) != -1) {
        //this part will be improved soon
        switch (path) {
            case "/":
                setHeadImportantData({});
                break;
            case "/services":
                setHeadImportantData({ title: "Nos services" });
                break;
            case "/help":
                setHeadImportantData({ title: "Aide, FAQ" });
                break;
            case "/packages":
                setHeadImportantData({ title: "Les packs que nous proposons" });
                break;
            case "/register":
                setHeadImportantData({ title: "Créer un compte" });
                break;
            case "/login":
                setHeadImportantData({ title: "Connectez-vous sur notre plateforme" });
                break;
            case "/reset-password":
                setHeadImportantData({ title: "Réinitialisation du mot de passe" });
                break;
            case "/contact":
                setHeadImportantData({ title: "Soyez en contacts avec nous" });
                break;
            case "/security":
                setHeadImportantData({ title: "La securité chez Usalvagetrade" });
                break;
            case "/about":
                setHeadImportantData({ title: "A propos de nous" });
                break;
            case "/terms":
                setHeadImportantData({ title: "Conditions d'utilisations" });
                break;
            case "/user/pack/subscribe":
                setHeadImportantData({ title: "Sourcription sur nos packs" });
                break;
            case "/user/share/link":
                setHeadImportantData({ title: "Pargtade d'un lien de parrainnage" });
                break;
            case "/user/dashboard":
                setHeadImportantData({ title: "Profil de l'utilisateur" });
                break;
            case "/user/history":
                setHeadImportantData({ title: "Historique de tous les retraits" });
                break;
            case "/user/tree":
                setHeadImportantData({ title: "Arbre de reseau de l'utilisateur" });
                break;
            case "/user/me":
                setHeadImportantData({ title: "Informations sur l'utilisateur" });
                break;
            case "/user/cashout":
                setHeadImportantData({ title: "Retrait de fonds" });
                break;
            default:
                setHeadImportantData({ title: "Page non trouvé" });
                break;
        }
    }
}
function setHeadImportantData(data) {
    const preTitle = data.title || "La révolution du commerce de la cryptomonnaie";
    const title = preTitle + " | USALVAGETRADE";
    document.title = title;
}
menuHighLighter();
class BinaryTree {
    /**
     * The actual data we goonna try to work with
     */
    data = { Id: "", icon: "", childs: [], name: "" };
    /**
     * The tree we gonna render
     */
    tree = [];
    imgPath = "/assets/img/";
    constructor(data) {
        if (data)
            this.data = data;
    }
    /**
     * Checks if any data tree given has children or not
     * @param data
     * @returns {boolean} Boolean
     */
    hasChildren(data) {
        return Array.isArray(data.childs) && data.childs.length > 0 ? true : false;
    }
    /**
     * Sets the root of the current treee
     */
    getAndSetRoot() {
        this.tree.push({
            id: this.data.Id,
            name: this.data.name,
            img: this.imgPath + this.data.icon,
        });
    }
    /**
     * Makes a needle tree data type for the library
     * @param data Tree object data
     */
    getAllChildrenFrom(data) {
        if (data.childs) {
            let length = data.childs.length, i = 0;
            for (i; i < length; i++) {
                if (this.hasChildren(data.childs[i])) {
                    this.getAllChildrenFrom(data.childs[i]);
                }
                this.tree.push({
                    pid: data.Id,
                    id: data.childs[i].Id,
                    name: data.childs[i].name,
                    img: this.imgPath + data.childs[i].icon,
                });
            }
        }
    }
    /**
     * Actually this the method that execute in which order our array will be fill in
     */
    drawTree() {
        this.getAndSetRoot();
        this.getAllChildrenFrom(this.data);
    }
}
const mediaQuery = window.matchMedia("(max-width:992px)");
let scaleInitial = 0.8;
if (mediaQuery.matches) {
    scaleInitial = 0.6;
}
/**
 * Uses the library to draw our tree and gets the data from ajax response
 * @param data Tree object data
 */
function drawBinaryTree(data) {
    const treeContainer = document.getElementById("binaryTreeContainer");
    if (treeContainer) {
        const bt = new BinaryTree(data);
        bt.drawTree();
        const chart = new OrgChart(document.getElementById("binaryTreeContainer"), {
            enableSearch: false,
            enableDragDrop: false,
            nodeTreeMenu: false,
            mouseScrool: OrgChart.none,
            scaleInitial,
            nodeBinding: {
                field_0: "name",
                img_0: "img",
            },
            nodes: bt.tree,
        });
    }
}
let binaryTreeData;
window.location.pathname === "/user/tree" &&
    $.post({
        method: "GET",
        url: "/user/tree-data",
        success: (data) => {
            const parsedData = JSON.parse(data);
            binaryTreeData = parsedData;
            drawBinaryTree(parsedData);
        },
    });
$("#copyMPS").click((e) => {
    e.preventDefault();
    navigator.clipboard.writeText($("#copyMPS").data("num")).then(() => {
        $("#copyMPS").html("Copié ! <i class='fas fa-check-circle ml-2'></i>");
    });
});
$("#copyAM").click((e) => {
    e.preventDefault();
    navigator.clipboard.writeText($("#copyAM").data("num")).then(() => {
        $("#copyAM").html("Copié ! <i class='fas fa-check-circle ml-2'></i>");
    });
});
$("#copyBTC").click((e) => {
    e.preventDefault();
    navigator.clipboard.writeText($("#copyBTC").data("addr")).then(() => {
        $("#copyBTC").html("Copié ! <i class='fas fa-check-circle ml-2'></i>");
    });
});
$("#copy").click((e) => {
    e.preventDefault();
    navigator.clipboard.writeText($("#valueToCopy").text()).then(() => {
        $("#copy").html("<i class='fas fa-check-circle'></i>");
    });
});
$("#showBTCGraph").click((e) => {
    e.preventDefault();
    $(BTCTransactionData).slideUp();
    $("#btcGraph").slideDown();
});
const passablePaths = "/user/pack/subscribe" || "/user/cashout";
if (window.location.pathname == passablePaths) {
    let socket = new WebSocket("wss://stream.binance.com:9443/ws/btcusdt@trade");
    let prices = [];
    function getPricesArray() {
        return new Promise((resolve, reject) => {
            socket.onmessage = (evt) => {
                let time = new Date().toString();
                if (time != null) {
                    time = time.match(/(.\d\:){2}\d{2}/gm);
                    if (time) {
                        time = time[0];
                        let seconds = time.split(":")[2];
                        let data_ = evt.data;
                        data_ = JSON.parse(data_);
                        if (prices.length > 9) {
                            prices.shift();
                        }
                        prices.push([time, parseInt(data_.p)]);
                        resolve(prices);
                    }
                }
            };
        });
    }
    setInterval(() => {
        google.charts.load("current", { packages: ["corechart"] });
        google.charts.setOnLoadCallback(drawChart);
        async function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ["time", "Price"],
                ...(await getPricesArray()),
            ]);
            var options = {
                title: "Prix BTC - USD",
                curveType: "function",
                legend: { position: "bottom" },
                series: {
                    Price: "#32e491",
                },
            };
            var chart = new google.visualization.LineChart(document.getElementById("btcGraph"));
            chart.draw(data, options);
        }
    }, 3000);
}
$("#validatedBtn").on("click", () => {
    $("#validated").slideDown();
    $("#unvalidated").slideUp();
    $("#historyTitle").text("Liste des retraits validés");
});
$("#unvalidatedBtn").on("click", () => {
    $("#validated").slideUp();
    $("#unvalidated").slideDown();
    $("#historyTitle").text("Liste des retraits non confirmés");
});
$("#zoomIn").on("click", () => {
    scaleInitial *= 2;
    drawBinaryTree(binaryTreeData);
});
$("#zoomOut").on("click", () => {
    scaleInitial *= 0.5;
    drawBinaryTree(binaryTreeData);
});
