(function () {
    'use strict';

    let body = $("body");

    /** Главный элемент виджета. */
    let mainSelectorElement = ".ar-image";
    let mainDomElement = $(mainSelectorElement);

    /** Все селекторы, которые использует модуль */
    let selector = {
        form: " form",
        skinUploadInput: mainSelectorElement + " .skin-file-input",
        uploadInput: mainSelectorElement + " input[type=file]",
        deleteButton: mainSelectorElement + " .delete-img a",
        deleteAllButton: mainSelectorElement + " .delete-all-img",

        btnExtraSmallCart: mainSelectorElement + " .extra-small-cart",
        btnSmallCart: mainSelectorElement + " .small-cart",
        btnNormalCart: mainSelectorElement + " .normal-cart",
        btnLargeCart: mainSelectorElement + " .large-cart",
        btnExtraLargeCart: mainSelectorElement + " .extra-large-cart",
    };

    /** Все DOM элементы, к которым обращается модуль. */
    let domElement = {
        /** Окно с карточками изображений. */
        previewAll: mainDomElement.find(".preview"),
        preview: (self) => {return $(self).closest(mainSelectorElement).find(".preview")},

        /** Карточки изображений. */
        cart: (self) => {
            let cart = $(self).closest(mainSelectorElement).find(".draggable");

            if (cart.length === 0) {
                cart = $(self).find(".draggable");
            }

            return cart ;
        },

        /** Размер карточек. */
        cartSize: (self) => {return $(self).closest(mainSelectorElement).find(".cart-size")},

        /** Карточки изображений уже загруженные на сервер. */
        cartOld: (self) => {return $(self).closest(mainSelectorElement).find(".draggable.old")},

        /** Карточки изображений выбранные для загрузки. */
        cartNew: (self) => {return $(self).closest(mainSelectorElement).find(".preview .draggable.new")},

        /** Элемент стилизованный под кнопку выбора изображений. */
        skinUploadInput: (self) => {return $(self).siblings(".skin-file-input")},

        /** Шаблон карточки, используемый для создания карточек, после выбора изображений. */
        exampleCart: mainDomElement.find(".example-cart"),

        /** Кнопка "большие значки". */
        btnLargeCart: (self) => {return $(self).siblings(".large-cart")},

        /** Кнопка "Маленькие значки". */
        btnSmallCart: (self) => {return $(self).siblings(".small-cart")},

        /** Кнопки размеров карточек. */
        btnSizeAll: (self) => {return $(self).siblings()},
    };

    /** Функции помощники */
    let utils = {
        /**
         * Изменение размера карточек.
         *
         * @param self object - this кнопки, которая изменяет размер
         * @param minHeight int - минимальная высота preview окна
         * @param sizeClass string - класс размера (col-lg-1 и т.д.)
         */
        resizeCart: function (self, minHeight, sizeClass) {
            if ($(self).attr("class").indexOf("active") === -1) {
                let cartSize = domElement.cartSize(self);

                domElement.preview(self).css({"min-height": minHeight + "px"});
                cartSize.removeAttr("class");
                cartSize.addClass("cart-size " + sizeClass);
                domElement.btnSizeAll(self).removeClass("active");
                $(self).addClass("active");
            }
        }
    }

    /** Событие выбора изображений для загрузки.  */
    body.on("change", selector.uploadInput, function () {
        let self = this,
            length = this.files.length,
            quantity = domElement.cart(self).length;

        if (length !== 0) {
            domElement.skinUploadInput(self).find("span").html("Выбрано изображений: " + length);
        } else {
            domElement.skinUploadInput(self).find("span").html("Выбрать изображения");
        }

        domElement.cartNew(self).closest("div").remove();
        for (let i = 0; i < length; i++) {
            let file = this.files[i],
                modifiedExampleCart;

            modifiedExampleCart = domElement.exampleCart.html()
                .replace(/{name}/gi, file.name)
                .replace(/{nameNew}/gi, file.name.split('.')[0])
                .replace(/{imgPosition}/gi, quantity)
                .replace(/{modified}/gi, file.lastModified);
            domElement.preview(self).append(modifiedExampleCart);

            if (domElement.preview(self).find(".list-records").length === 0) {
                let fileReader = new FileReader();
                fileReader.readAsDataURL(file);
                fileReader.onload = function () {
                    let myImg = domElement.preview(self).find(`img[data-modified=${file.lastModified}]`);

                    myImg.attr({src: fileReader.result});
                }
            }

            quantity++;
        }
    });

    /** Событие удаления изображения, которое уже находится на сервере. */
    body.on("click", selector.deleteButton, function (event) {
        event.preventDefault();

        if (confirm("Вы уверены, что хотите удалить изображение?")) {
            $(this).closest(".cart-size").remove();
        }
    });

    /** Событие удаления всех изображений, которые уже находятся на сервере. */
    body.on("click", selector.deleteAllButton, function () {
        if (confirm("Вы уверены, что хотите удалить все изображения?")) {
            domElement.cartOld(this).closest(".cart-size").remove();
        }
    });

    /** События переключения карточек на список */
    body.on("click", selector.btnExtraSmallCart, function () {
        utils.resizeCart(this, 245, "list-records");
    });

    /** Событие переключения карточек на маленький размер */
    body.on("click", selector.btnSmallCart, function () {
        utils.resizeCart(this, 157, "col-lg-1");
    });

    /** Событие переключения карточек на средний размер */
    body.on("click", selector.btnNormalCart, function () {
        utils.resizeCart(this, 245, "col-lg-2");
    });

    /** Событие переключения карточек на большой размер */
    body.on("click", selector.btnLargeCart, function () {
        utils.resizeCart(this, 437, "col-lg-4");
    });

    /** Событие переключения карточек на огромный размер */
    body.on("click", selector.btnExtraLargeCart, function () {
        utils.resizeCart(this, 635, "col-lg-6");
    });

    /** Вызов fileInput по кастомизированной кнопке выбора файлов. */
    body.on("click", selector.skinUploadInput, function () {
        $(this).siblings("input[type=file]").click();
    });

    /** Изменение значения "position" при отправке формы */
    body.on("submit", selector.form, function () {
        domElement.cart(this).each(function (index, element) {
            $(element).find("input").val(index);
        })
    });

    /** Настройка draggable от jquery ui. */
    (function() {
        domElement.previewAll.sortable({
            containment: "parent",
            tolerance: "pointer",
            scroll: false,
        });
    }());
}());