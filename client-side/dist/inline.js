/// <reference path="../../dist/tinymce/tinymce.d.ts" />
var Inline = (function () {
    function Inline() {
        this.changes = {};
        this.btns = {};
        this.editableConfigs = {
            'headings': {
                selector: 'h1.inline-editing, h2.inline-editing, h3.inline-editing, h4.inline-editing, h5.inline-editing, h6.inline-editing',
                toolbar: 'italic strikethrough | nonbreaking | undo redo',
            },
            'inlines': {
                selector: 'span.inline-editing, strong.inline-editing, a.inline-editing',
                forced_root_block: '',
                toolbar: 'bold italic strikethrough | nonbreaking | link | undo redo'
            },
            'blocks': {
                selector: 'div.inline-editing',
                toolbar: 'bold italic strikethrough | nonbreaking | fontsizeselect | styleselect bullist numlist link image | undo redo',
                style_formats: [
                    {
                        title: 'Nadpisy',
                        items: [
                            { title: 'Nadpis 1', format: 'h1' },
                            { title: 'Nadpis 2', format: 'h2' },
                            { title: 'Nadpis 3', format: 'h3' },
                            { title: 'Nadpis 4', format: 'h4' },
                            { title: 'Nadpis 5', format: 'h5' },
                            { title: 'Nadpis 6', format: 'h6' }
                        ]
                    },
                    { title: 'Horní index', icon: 'superscript', format: 'superscript' },
                    { title: 'Dolní index', icon: 'subscript', format: 'subscript' },
                    { title: 'Zarovnání', icon: 'alignleft', items: [
                            { title: 'Doleva', icon: 'alignleft', format: 'alignleft' },
                            { title: 'Na střed', icon: 'aligncenter', format: 'aligncenter' },
                            { title: 'Doprava', icon: 'alignright', format: 'alignright' },
                            { title: 'Do bloku', icon: 'alignjustify', format: 'alignjustify' }
                        ]
                    }
                ]
            }
        };
        var source = document.getElementById('inline-editing-source');
        if (!source) {
            return;
        }
        var cssLink = document.createElement('link');
        cssLink.href = source.getAttribute('data-source-css');
        cssLink.setAttribute('rel', 'stylesheet');
        cssLink.setAttribute('type', 'text/css');
        document.head.appendChild(cssLink);
        this.gatewayUrl = source.getAttribute('data-source-gateway-url');
        if (typeof tinymce === 'undefined') {
            var tinymceLink = document.createElement('script');
            tinymceLink.src = source.getAttribute('data-source-tinymce-js');
            var self_1 = this;
            tinymceLink.onload = function () {
                self_1.initUI();
            };
            document.head.insertBefore(tinymceLink, document.head.firstChild);
        }
        else {
            this.initUI();
        }
    }
    Inline.prototype.initUI = function () {
        var self = this;
        var container = document.createElement('div');
        container.classList.add('inline-container');
        var btnEnable = this.btns['enable'] = document.createElement('button');
        btnEnable.innerHTML = '<i class="inline-icon-xcore"></i>';
        btnEnable.className = 'inline-editing-btn inline-enable';
        btnEnable.addEventListener('click', function () { return self.enable(); });
        container.appendChild(btnEnable);
        var btnDisable = this.btns['disable'] = document.createElement('button');
        btnDisable.innerHTML = '<i class="inline-icon-xcore"></i>';
        btnDisable.className = 'inline-editing-btn inline-disable inline-hidden';
        btnDisable.addEventListener('click', function () { return self.disable(); });
        container.appendChild(btnDisable);
        var btnStatus = this.btns['status'] = document.createElement('button');
        btnStatus.className = 'inline-editing-btn inline-status inline-hidden';
        container.appendChild(btnStatus);
        var btnSave = this.btns['save'] = document.createElement('button');
        btnSave.innerHTML = '<i class="inline-icon-save"></i>';
        btnSave.className = 'inline-editing-btn inline-save inline-hidden inactive';
        btnSave.addEventListener('click', function () { return self.saveAll(); });
        container.appendChild(btnSave);
        var btnRevert = this.btns['revert'] = document.createElement('button');
        btnRevert.innerHTML = '<i class="inline-icon-trash"></i>';
        btnRevert.className = 'inline-editing-btn inline-revert inline-hidden inactive';
        btnRevert.addEventListener('click', function () { return self.revertAll(); });
        container.appendChild(btnRevert);
        document.body.appendChild(container);
        this.initTinymce();
    };
    Inline.prototype.initTinymce = function () {
        var self = this;
        this.editables = document.querySelectorAll('*[data-inline-name]');
        this.editablesForeach(function (el) {
            el.classList.add('inline-editing');
            el.classList.add('inline-disabled');
        });
        this.backup();
        for (var optionsName in this.editableConfigs) {
            var settings = Object.assign({
                entity_encoding: 'raw',
                inline: true,
                menubar: false,
                language: 'cs',
                plugins: 'paste link image lists nonbreaking',
                paste_as_text: true,
                theme: 'modern',
                setup: function (editor) {
                    editor.on('init', function () { return self.disable(); });
                    editor.on('keyup change redo undo', function () {
                        self.updateContent(editor);
                    });
                }
            }, this.editableConfigs[optionsName]);
            tinymce.init(settings);
        }
    };
    Inline.prototype.updateContent = function (editor) {
        var el = editor.bodyElement;
        var key = el.id;
        if (this.changes.hasOwnProperty(key)) {
            this.changes[key].content = editor.getContent();
        }
        else {
            this.changes[key] = new Item(el.dataset['inlineNamespace'], el.dataset['inlineLocale'], el.dataset['inlineName'], editor.getContent());
        }
        this.btns['status'].classList.add('inline-hidden');
        this.btns['save'].classList.remove('inactive');
        this.btns['revert'].classList.remove('inactive');
    };
    Inline.prototype.saveAll = function () {
        var saveBtn = this.btns['save'];
        if (saveBtn.classList.contains('inactive')) {
            return;
        }
        var statusBtn = this.btns['status'];
        statusBtn.classList.remove('inline-hidden');
        statusBtn.innerHTML = '<i class="inline-icon-progress"></i>';
        var self = this;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.gatewayUrl);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function () {
            if (xhr.status === 200) {
                statusBtn.innerHTML = '<i class="inline-icon-ok"></i>';
            }
            else {
                statusBtn.innerHTML = '<i class="inline-icon-error"></i>';
            }
            saveBtn.classList.add('inactive');
            self.btns['revert'].classList.add('inactive');
            self.changes = {};
            self.backup();
        };
        xhr.send(JSON.stringify(this.changes));
    };
    Inline.prototype.revertAll = function () {
        this.editablesForeach(function (el) { return el.innerHTML = el.getAttribute('data-inline-backup'); });
        this.btns['save'].classList.add('inactive');
        this.btns['revert'].classList.add('inactive');
        this.changes = {};
    };
    Inline.prototype.enable = function () {
        this.btns['enable'].classList.add('inline-hidden');
        this.btns['disable'].classList.remove('inline-hidden');
        this.btns['save'].classList.remove('inline-hidden');
        this.btns['revert'].classList.remove('inline-hidden');
        this.editablesForeach(function (el) {
            el.classList.remove('inline-disabled');
            el.setAttribute('contenteditable', 'true');
        });
    };
    Inline.prototype.disable = function () {
        this.btns['disable'].classList.add('inline-hidden');
        this.btns['status'].classList.add('inline-hidden');
        this.btns['save'].classList.add('inline-hidden');
        this.btns['revert'].classList.add('inline-hidden');
        this.btns['enable'].classList.remove('inline-hidden');
        this.editablesForeach(function (el) {
            el.classList.add('inline-disabled');
            el.setAttribute('contenteditable', 'false');
        });
    };
    Inline.prototype.backup = function () {
        this.editablesForeach(function (el) { return el.setAttribute('data-inline-backup', el.innerHTML); });
    };
    Inline.prototype.editablesForeach = function (callback) {
        for (var i = 0; i < this.editables.length; i++) {
            callback(this.editables[i]);
        }
    };
    return Inline;
}());
var Item = (function () {
    function Item(namespace, locale, name, content) {
        this.namespace = namespace;
        this.locale = locale;
        this.name = name;
        this.content = content;
    }
    return Item;
}());
document.addEventListener('DOMContentLoaded', function () {
    var inline = new Inline();
});
