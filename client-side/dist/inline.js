var Inline = (function () {
    function Inline() {
        var _this = this;
        this.changes = {};
        this.btns = {};
        this.editableConfigs = {
            'headings': {
                selector: 'h1.inline-editing-tinymce, h2.inline-editing-tinymce, h3.inline-editing-tinymce, h4.inline-editing-tinymce, h5.inline-editing-tinymce, h6.inline-editing-tinymce',
                toolbar: 'italic strikethrough | nonbreaking | undo redo',
            },
            'inlines': {
                selector: 'span.inline-editing-tinymce, strong.inline-editing-tinymce, a.inline-editing-tinymce',
                forced_root_block: '',
                toolbar: 'bold italic strikethrough | nonbreaking | link | undo redo'
            },
            'blocks': {
                selector: 'div.inline-editing-tinymce',
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
                    {
                        title: 'Zarovnání', icon: 'alignleft', items: [
                            { title: 'Doleva', icon: 'alignleft', format: 'alignleft' },
                            { title: 'Na střed', icon: 'aligncenter', format: 'aligncenter' },
                            { title: 'Doprava', icon: 'alignright', format: 'alignright' },
                            { title: 'Do bloku', icon: 'alignjustify', format: 'alignjustify' }
                        ]
                    }
                ]
            }
        };
        this.updateSpecificContent = function (evt) {
            var el = evt.target;
            var key = el.id;
            if (_this.changes.hasOwnProperty(key)) {
                _this.changes[key].content = el.textContent;
            }
            else {
                _this.changes[key] = new EntityItem(el.dataset['inlineEntity'], el.dataset['inlineId'], el.dataset['inlineProperty'], el.textContent);
            }
            _this.btns['status'].classList.add('inline-hidden');
            _this.btns['save'].classList.remove('inactive');
            _this.btns['revert'].classList.remove('inactive');
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
            tinymceLink.onload = function () { return _this.initUI(); };
            document.head.insertBefore(tinymceLink, document.head.firstChild);
        }
        else {
            this.initUI();
        }
    }
    Inline.prototype.initUI = function () {
        var _this = this;
        var container = document.createElement('div');
        container.classList.add('inline-container');
        var btnEnable = this.btns['enable'] = document.createElement('button');
        btnEnable.innerHTML = '<i class="inline-icon-xcore"></i>';
        btnEnable.className = 'inline-editing-btn inline-enable';
        btnEnable.addEventListener('click', function () { return _this.enable(); });
        container.appendChild(btnEnable);
        var btnDisable = this.btns['disable'] = document.createElement('button');
        btnDisable.innerHTML = '<i class="inline-icon-xcore"></i>';
        btnDisable.className = 'inline-editing-btn inline-disable inline-hidden';
        btnDisable.addEventListener('click', function () { return _this.disable(); });
        container.appendChild(btnDisable);
        var btnStatus = this.btns['status'] = document.createElement('button');
        btnStatus.className = 'inline-editing-btn inline-status inline-hidden';
        container.appendChild(btnStatus);
        var btnSave = this.btns['save'] = document.createElement('button');
        btnSave.innerHTML = '<i class="inline-icon-save"></i>';
        btnSave.className = 'inline-editing-btn inline-save inline-hidden inactive';
        btnSave.addEventListener('click', function () { return _this.saveAll(); });
        container.appendChild(btnSave);
        var btnRevert = this.btns['revert'] = document.createElement('button');
        btnRevert.innerHTML = '<i class="inline-icon-trash"></i>';
        btnRevert.className = 'inline-editing-btn inline-revert inline-hidden inactive';
        btnRevert.addEventListener('click', function () { return _this.revertAll(); });
        container.appendChild(btnRevert);
        document.body.appendChild(container);
        this.preInitTinymce();
    };
    Inline.prototype.preInitTinymce = function () {
        this.editables = document.querySelectorAll('*[data-inline-type]');
        this.editablesForeach(function (el) {
            var type = el.getAttribute('data-inline-type');
            el.classList.add((type === 'simple' || type === 'entity') ? 'inline-editing-tinymce' : 'inline-editing-specific');
        });
        this.backup();
    };
    Inline.prototype.updateTinymceContent = function (editor) {
        var el = editor.bodyElement;
        var key = el.id;
        if (this.changes.hasOwnProperty(key)) {
            this.changes[key].content = editor.getContent();
        }
        else {
            if (el.dataset['inlineType'] === 'simple') {
                this.changes[key] = new SimpleItem(el.dataset['inlineNamespace'], el.dataset['inlineLocale'], el.dataset['inlineName'], editor.getContent());
            }
            else if (el.dataset['inlineType'] === 'entity') {
                this.changes[key] = new EntityItem(el.dataset['inlineEntity'], el.dataset['inlineId'], el.dataset['inlineProperty'], editor.getContent());
            }
            else {
                console.log('invalid type');
            }
        }
        this.btns['status'].classList.add('inline-hidden');
        this.btns['save'].classList.remove('inactive');
        this.btns['revert'].classList.remove('inactive');
    };
    Inline.prototype.saveAll = function () {
        var _this = this;
        var saveBtn = this.btns['save'];
        if (saveBtn.classList.contains('inactive')) {
            return;
        }
        var statusBtn = this.btns['status'];
        statusBtn.classList.remove('inline-hidden');
        statusBtn.innerHTML = '<i class="inline-icon-progress"></i>';
        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.gatewayUrl);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function () {
            _this.clearErrors();
            if (xhr.status === 200) {
                statusBtn.innerHTML = '<i class="inline-icon-ok"></i>';
                _this.changes = {};
                _this.backup();
                _this.btns['revert'].classList.add('inactive');
            }
            else {
                statusBtn.innerHTML = '<i class="inline-icon-error"></i>';
            }
            var statusData = [];
            try {
                statusData = JSON.parse(xhr.responseText);
            }
            catch (e) {
                alert('Server error (json)');
            }
            var _loop_1 = function (editableId) {
                var el = document.getElementById(editableId);
                if (el) {
                    switch (statusData[editableId].status) {
                        case 0:
                            delete _this.changes[editableId];
                            el.classList.add('inline-content-success');
                            setTimeout(function () { return el.classList.remove('inline-content-success'); }, 500);
                            break;
                        case 1:
                            el.classList.add('inline-content-warning');
                            break;
                        default:
                            el.classList.add('inline-content-error');
                            var errMsg = document.createElement('span');
                            errMsg.classList.add('inline-error-msg');
                            errMsg.innerHTML = statusData[editableId].message;
                            el.parentNode.insertBefore(errMsg, el.nextSibling);
                            break;
                    }
                }
                else if (statusData[editableId].message) {
                    alert(statusData[editableId].message);
                }
                else {
                    alert('Server error (unknown)');
                }
            };
            for (var editableId in statusData) {
                _loop_1(editableId);
            }
            saveBtn.classList.add('inactive');
        };
        xhr.send(JSON.stringify(this.changes));
    };
    Inline.prototype.clearErrors = function () {
        this.editablesForeach(function (el) { return el.classList.remove('inline-content-error', 'inline-content-warning'); });
        var msgElems = document.querySelectorAll('.inline-error-msg');
        for (var i = 0; i < msgElems.length; i++) {
            msgElems[i].remove();
        }
    };
    Inline.prototype.revertAll = function () {
        this.clearErrors();
        this.editablesForeach(function (el) { return el.innerHTML = el.getAttribute('data-inline-backup'); });
        this.btns['save'].classList.add('inactive');
        this.btns['status'].classList.add('inline-hidden');
        this.btns['revert'].classList.add('inactive');
        this.changes = {};
    };
    Inline.prototype.enable = function () {
        var _this = this;
        this.btns['enable'].classList.add('inline-hidden');
        this.btns['disable'].classList.remove('inline-hidden');
        this.btns['save'].classList.remove('inline-hidden');
        this.btns['revert'].classList.remove('inline-hidden');
        this.editablesForeach(function (el) { return el.classList.add('inline-editing'); });
        for (var optionsName in this.editableConfigs) {
            var settings = Object.assign({
                entity_encoding: 'raw',
                inline: true,
                menubar: false,
                language: 'cs',
                plugins: 'paste link image lists nonbreaking',
                paste_as_text: true,
                theme: 'modern',
                setup: function (editor) { return editor.on('keyup change redo undo', function () { return _this.updateTinymceContent(editor); }); }
            }, this.editableConfigs[optionsName]);
            tinymce.init(settings);
        }
        this.applySpecificEditable();
    };
    Inline.prototype.disable = function () {
        this.btns['disable'].classList.add('inline-hidden');
        this.btns['status'].classList.add('inline-hidden');
        this.btns['save'].classList.add('inline-hidden');
        this.btns['revert'].classList.add('inline-hidden');
        this.btns['enable'].classList.remove('inline-hidden');
        this.editablesForeach(function (el) { return el.classList.remove('inline-editing'); });
        tinymce.remove();
        this.removeSpecificEditable();
    };
    Inline.prototype.backup = function () {
        this.editablesForeach(function (el) { return el.setAttribute('data-inline-backup', el.innerHTML); });
    };
    Inline.prototype.editablesForeach = function (callback) {
        for (var i = 0; i < this.editables.length; i++) {
            callback(this.editables[i]);
        }
    };
    Inline.prototype.applySpecificEditable = function () {
        var _this = this;
        this.editablesForeach(function (el) {
            if (el.classList.contains('inline-editing-specific')) {
                el.addEventListener('keypress', function (evt) {
                    if (evt.which === 13) {
                        evt.preventDefault();
                    }
                });
                el.addEventListener('keyup', _this.updateSpecificContent);
                el.addEventListener('change', _this.updateSpecificContent);
                el.addEventListener('redo', _this.updateSpecificContent);
                el.addEventListener('undo', _this.updateSpecificContent);
                el.setAttribute('contenteditable', 'true');
            }
        });
    };
    Inline.prototype.removeSpecificEditable = function () {
        var _this = this;
        this.editablesForeach(function (el) {
            if (el.classList.contains('inline-editing-specific')) {
                el.removeEventListener('keyup', _this.updateSpecificContent);
                el.removeEventListener('change', _this.updateSpecificContent);
                el.removeEventListener('redo', _this.updateSpecificContent);
                el.removeEventListener('undo', _this.updateSpecificContent);
                el.removeAttribute('contenteditable');
            }
        });
    };
    return Inline;
}());
var SimpleItem = (function () {
    function SimpleItem(namespace, locale, name, content) {
        this.namespace = namespace;
        this.locale = locale;
        this.name = name;
        this.content = content;
        this.type = 'simple';
    }
    return SimpleItem;
}());
var EntityItem = (function () {
    function EntityItem(entity, id, property, content) {
        this.entity = entity;
        this.id = id;
        this.property = property;
        this.content = content;
        this.type = 'entity';
    }
    return EntityItem;
}());
document.addEventListener('DOMContentLoaded', function () { return window.xcoreInline = new Inline; });
