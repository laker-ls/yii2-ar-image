(function () {
    'use strict';

    /** Главный DOM элемент. */
    let mainSelectorElement = ".ar-image";
    let mainDomElement = $(mainSelectorElement);

    /** Все DOM элементы, к которым обращается модуль. */
    let domElement = {
        /** Форма отправки. */
        form: mainDomElement.closest("form"),

        /** Окно с карточками изображений. */
        previewAll: mainDomElement.find(".preview"),
        preview: (self) => {return $(self).closest(mainSelectorElement).find(".preview")},

        /** Карточки изображений */
        cart: (self) => {
            let cart = $(self).closest(mainSelectorElement).find(".draggable");

            if (cart.length === 0) {
                cart = $(self).find(".draggable");
            }

            return cart ;
        },

        /** Карточки изображений уже загруженные на сервер. */
        cartOld: (self) => {return $(self).closest(mainSelectorElement).find(".draggable.old")},

        /** Карточки изображений выбранные для загрузки. */
        cartNew: (self) => {return $(self).closest(mainSelectorElement).find(".preview .draggable.new")},

        /** Инпут выбора изображений. */
        uploadInput: mainDomElement.find("input[type=file]"),

        /** Элемент стилизованный под кнопку выбора изображений. */
        skinUploadInputAll: mainDomElement.find(".skin-file-input"),
        skinUploadInput: (self) => {return $(self).siblings(".skin-file-input")},

        /** "Кнопка" удаления загруженного на сервер изображения. */
        deleteButton: mainDomElement.find(".delete-img a"),

        /** "Кнопка" удаления всех загруженных на сервер изображений. */
        deleteAllButton: mainDomElement.find(".delete-all-img"),

        /** Шаблон карточки, используемый для создания карточек, после выбора изображений. */
        exampleCart: mainDomElement.find(".example-cart"),

        /** Кнопка "большие значки" */
        btnLargeCartAll: mainDomElement.find(".large-cart"),
        btnLargeCart: (self) => {return $(self).siblings(".large-cart")},

        /** Кнопка "Маленькие значки" */
        btnSmallCartAll: mainDomElement.find(".small-cart"),
        btnSmallCart: (self) => {return $(self).siblings(".small-cart")},
    };

    /** Событие выбора изображений для загрузки.  */
    domElement.uploadInput.on("change", function () {
        let self = this,
            length = this.files.length,
            quantity = domElement.cart(self).length;

        if (length !== 0) {
            domElement.skinUploadInput(self).find("span").html("Выбрано изображений: " + length);
        } else {
            domElement.skinUploadInput(self).find("span").html("Выбрать изображения");
        }

        domElement.cartNew(self).remove();
        for (let i = 0; i < length; i++) {
            let fileReader = new FileReader(),
                file = this.files[i];

            fileReader.readAsDataURL(file);
            fileReader.onload = function () {
                let modifiedExampleCart;

                modifiedExampleCart = domElement.exampleCart.html()
                    .replace(/{imgSrc}/gi, fileReader.result)
                    .replace(/{name}/gi, file.name)
                    .replace(/{nameNew}/gi, file.name.split('.')[0])
                    .replace(/{imgPosition}/gi, quantity);

                domElement.preview(self).append(modifiedExampleCart);

                quantity++;
            }
        }
    });

    /** Событие удаления изображения, которое уже находится на сервере. */
    domElement.deleteButton.on("click", function (event) {
        event.preventDefault();

        if (confirm("Вы уверены, что хотите удалить изображение?")) {
            $(this).closest(".draggable").remove();
        }
    });

    /** Событие удаления всех изображений, которые уже находятся на сервере. */
    domElement.deleteAllButton.on("click", function () {
        if (confirm("Вы уверены, что хотите удалить все изображения?")) {
            domElement.cartOld(this).remove();
        }
    });

    /** Событие переключение карточек на большой размер */
    domElement.btnLargeCartAll.on("click", function () {
        if ($(this).attr("class").indexOf("active") === -1) {
            domElement.preview(this).css({"min-height": "258px"});
            domElement.cart(this).removeClass("col-lg-1");
            domElement.cart(this).addClass("col-lg-2");
            domElement.btnSmallCart(this).removeClass("active");
            $(this).addClass("active");
        }
    });

    /** Событие переключение карточек на маленький размер */
    domElement.btnSmallCartAll.on("click", function () {
        if ($(this).attr("class").indexOf("active") === -1) {
            domElement.preview(this).css({"min-height": "157px"});
            domElement.cart(this).removeClass("col-lg-2");
            domElement.cart(this).addClass("col-lg-1");
            domElement.btnLargeCart(this).removeClass("active");
            $(this).addClass("active");
        }
    });

    /** Вызов fileInput по кастомизированной кнопке выбора файлов. */
    domElement.skinUploadInputAll.on("click", function () {
        $(this).siblings("input[type=file]").click();
    });

    /** Изменение значения "position" при отправке формы */
    domElement.form.on("submit", function () {
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