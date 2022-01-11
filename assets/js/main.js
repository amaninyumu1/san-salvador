"use strict";
var $;
(function () {
    var handler = document.querySelector("#menuHandler");
    var menu = document.querySelector("#menu");
    handler &&
        handler.addEventListener("click", function () {
            $("#menuContainer").css("padding", "15px");
            $(menu).slideToggle();
        });
})();
/**
 * All these lines bellow concern menu humberger
 */
var toggleButton = document.getElementById("toggle-button");
if (toggleButton) {
    var iconButton_1 = toggleButton.querySelector(".fas");
    var navbar_1 = document.getElementById("navbar");
    toggleButton.addEventListener("click", function () {
        navbar_1 && navbar_1.classList.toggle("hidden");
        if (iconButton_1)
            if (iconButton_1.classList.contains("fa-bars")) {
                iconButton_1.classList.remove("fa-bars");
                iconButton_1.classList.add("fa-times");
            }
            else {
                iconButton_1.classList.remove("fa-times");
                iconButton_1.classList.add("fa-bars");
            }
    });
}
//End of menu humberger code
/**
 * This function handles the admin time each minutes.
 */
var timeHandler = function () {
    var time = new Date;
    var adminTimer = document.querySelector("#adminTimer");
    if (adminTimer) {
        adminTimer.textContent = time.getHours() + " : " + time.getMinutes();
    }
};
$(document).ready(function () {
    setInterval(function () { return timeHandler(); }, 10000);
    $("#hamburger").on("click", function () {
        $("#other").slideUp("slow");
        $("#mobile").slideDown("slow");
    });
    $("#times").on("click", function () {
        $("#mobile").slideUp("slow");
        $("#other").slideDown("slow");
    });
    $("#year").text(new Date().getFullYear().toString());
});
var imageUpload = document.querySelector("#image");
$(".hide-b4-save").hide();
/**
 * Initializing cropper class
 */
var Cropper;
var cropper = new Cropper({
    width: 320,
    height: 320,
    onChange: function () {
        var image = this.getCroppedImage();
        var file = dataURLtoFile(image, "user");
        if (imageUpload && file && imageUpload.files) {
            var dataTransfer = new DataTransfer();
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
    var array = dataUrl.split(",");
    if (array !== null) {
        var matchExtension = array[0].match(/data:(.*);/);
        var matchMimeType = array[0].match(/:(.*?);/);
        var extension = void 0;
        var mime = void 0;
        if (matchExtension !== null) {
            extension = matchExtension[1].split("/")[1];
            if (matchMimeType !== null) {
                mime = matchMimeType[1];
                var bstr = atob(array[1]);
                var n = bstr.length;
                var u8array = new Uint8Array(n);
                while (n--) {
                    u8array[n] = bstr.charCodeAt(n);
                }
                return new File([u8array], filename + "." + extension, { type: mime });
            }
        }
    }
}
var imageUploader = document.querySelector("#imageToCrop");
imageUploader && imageUploader.addEventListener("change", function (evt) {
    if (evt.target !== null) {
        var target = evt.target;
        var image = target.files[0];
        if (image) {
            var fileReader_1 = new FileReader();
            fileReader_1.onload = function () {
                cropper.loadImage(fileReader_1.result).then(function () { });
                $("#crop").slideDown();
                $("#crop").removeClass(".hidden");
                $(".hide-b4-save").slideDown();
            };
            fileReader_1.readAsDataURL(image);
        }
    }
});
document.querySelector("#crop") && cropper.render("#crop");
//Add user interactions stars here
var formStepsButtons = document.querySelectorAll(".form-user-btn");
formStepsButtons && formStepsButtons.forEach(function (button) {
    var buttonName = button.name;
    $(button).on("click", function (e) {
        e.preventDefault();
        useFormButtons(buttonName);
    });
    var cameraBtn = document.querySelector("#camera");
    if (cameraBtn && imageUploader)
        cameraBtn.addEventListener("click", function (e) {
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
var userMenus = document.querySelectorAll("[data-path-user]");
console.log(userMenus);
userMenus.forEach(function (menu) {
    menu.addEventListener("click", function () {
        var path = menu.getAttribute("data-path-user");
        if (path) {
            window.location.pathname = path;
        }
    });
});
/**
 * Pairing sides
 */
var paringSides = document.querySelector("#pairing-sides");
var sides = paringSides === null || paringSides === void 0 ? void 0 : paringSides.querySelectorAll("[data-side]");
sides === null || sides === void 0 ? void 0 : sides.forEach(function (side) {
    side.addEventListener("click", function (e) {
        var sideId = side.getAttribute("data-side");
        setActiveSide(side);
    });
});
function setActiveSide(side) {
    $(side).attr("class", "flex border border-gray-400 cursor-pointer bg-green-500 text-gray-900 rounded h-12 p-1 items-center w-4/12 justify-between");
    $(side).siblings().attr("class", "flex border border-gray-400 cursor-pointer text-gray-300 rounded h-12 p-1 items-center w-4/12 justify-between");
    var sideCircle = side === null || side === void 0 ? void 0 : side.querySelector("span:nth-child(2)");
    $($(side).siblings().children()[1]).html("<i></i>");
    $(sideCircle).attr("class", "h-7 w-7 rounded-full border border-gray-900 grid place-items-center bg-gray-900");
    $(sideCircle).html("<i class='fas fa-check-circle text-green-500'></i>");
}
$("#copy").click(function () {
    var valueToCopy = document.querySelector("#valueToCopy");
});
