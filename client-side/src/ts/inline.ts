/// <reference path="../../dist/tinymce/tinymce.d.ts" />

import Editor = TinyMCE.Editor

class Inline {

    public gatewayUrl: string

    public editables: NodeListOf<Element>

    public changes: {[id: string]: BaseItem} = {}

    public btns: {[id: string]: HTMLButtonElement} = {}

    public editableConfigs: any = {
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
                        {title: 'Nadpis 1', format: 'h1'},
                        {title: 'Nadpis 2', format: 'h2'},
                        {title: 'Nadpis 3', format: 'h3'},
                        {title: 'Nadpis 4', format: 'h4'},
                        {title: 'Nadpis 5', format: 'h5'},
                        {title: 'Nadpis 6', format: 'h6'}]
                },
                {title: 'Horní index', icon: 'superscript', format: 'superscript'},
                {title: 'Dolní index', icon: 'subscript', format: 'subscript'},
                {title: 'Zarovnání', icon: 'alignleft', items: [
                    {title: 'Doleva', icon: 'alignleft', format: 'alignleft'},
                    {title: 'Na střed', icon: 'aligncenter', format: 'aligncenter'},
                    {title: 'Doprava', icon: 'alignright', format: 'alignright'},
                    {title: 'Do bloku', icon: 'alignjustify', format: 'alignjustify'}]
                }
            ]
        }
    }

    public constructor() {
        let source = document.getElementById('inline-editing-source')

        if (!source) {
            return
        }

        let cssLink = document.createElement('link')
        cssLink.href = source.getAttribute('data-source-css')
        cssLink.setAttribute('rel', 'stylesheet')
        cssLink.setAttribute('type', 'text/css')
        document.head.appendChild(cssLink)

        this.gatewayUrl = source.getAttribute('data-source-gateway-url')

        if (typeof tinymce === 'undefined') {
            let tinymceLink = document.createElement('script')
            tinymceLink.src = source.getAttribute('data-source-tinymce-js')
            let self = this
            tinymceLink.onload = function () {
                self.initUI()
            }
            document.head.insertBefore(tinymceLink, document.head.firstChild)
        } else {
            this.initUI()
        }
    }

    public initUI() {
        let self = this

        let container = document.createElement('div')
        container.classList.add('inline-container')

        let btnEnable = this.btns['enable'] = document.createElement('button')
        btnEnable.innerHTML = '<i class="inline-icon-xcore"></i>'
        btnEnable.className = 'inline-editing-btn inline-enable'
        btnEnable.addEventListener('click', () => self.enable())
        container.appendChild(btnEnable)

        let btnDisable = this.btns['disable'] = document.createElement('button');
        btnDisable.innerHTML = '<i class="inline-icon-xcore"></i>'
        btnDisable.className = 'inline-editing-btn inline-disable inline-hidden'
        btnDisable.addEventListener('click', () => self.disable())
        container.appendChild(btnDisable)

        let btnStatus = this.btns['status'] = document.createElement('button');
        btnStatus.className = 'inline-editing-btn inline-status inline-hidden'
        container.appendChild(btnStatus)

        let btnSave = this.btns['save'] = document.createElement('button')
        btnSave.innerHTML = '<i class="inline-icon-save"></i>'
        btnSave.className = 'inline-editing-btn inline-save inline-hidden inactive'
        btnSave.addEventListener('click', () => self.saveAll())
        container.appendChild(btnSave)

        let btnRevert = this.btns['revert'] = document.createElement('button')
        btnRevert.innerHTML = '<i class="inline-icon-trash"></i>'
        btnRevert.className = 'inline-editing-btn inline-revert inline-hidden inactive'
        btnRevert.addEventListener('click', () => self.revertAll())
        container.appendChild(btnRevert)

        document.body.appendChild(container)

        this.preInitTinymce()
    }

    public preInitTinymce() {

        this.editables = document.querySelectorAll('*[data-inline-type]')

        this.editablesForeach((el) => {
            el.classList.add('inline-editing')
            el.classList.add('inline-disabled')
        })

        this.backup()
    }

    public updateContent(editor: Editor) {

        let el = editor.bodyElement
        let key = el.id

        if (this.changes.hasOwnProperty(key)) {
            this.changes[key].content = editor.getContent()
        } else {
            if (el.dataset['inlineType'] === 'simple') {
                this.changes[key] = new SimpleItem(
                    el.dataset['inlineNamespace'],
                    el.dataset['inlineLocale'],
                    el.dataset['inlineName'],
                    editor.getContent()
                )
            } else if (el.dataset['inlineType'] === 'entity') {
                this.changes[key] = new EntityItem(
                    el.dataset['inlineEntity'],
                    el.dataset['inlineId'],
                    el.dataset['inlineProperty'],
                    editor.getContent()
                )
            } else {
                console.log('invalid type')
            }
        }

        this.btns['status'].classList.add('inline-hidden')
        this.btns['save'].classList.remove('inactive')
        this.btns['revert'].classList.remove('inactive')
    }

    public saveAll() {

        let saveBtn = this.btns['save'];
        if (saveBtn.classList.contains('inactive')) {
            return
        }

        let statusBtn = this.btns['status'];
        statusBtn.classList.remove('inline-hidden')
        statusBtn.innerHTML = '<i class="inline-icon-progress"></i>'

        let self = this
        let xhr = new XMLHttpRequest()

        xhr.open('POST', this.gatewayUrl)
        xhr.setRequestHeader('Content-Type', 'application/json')
        xhr.onload = function () {
            if (xhr.status === 200) {
                statusBtn.innerHTML = '<i class="inline-icon-ok"></i>'
            } else {
                statusBtn.innerHTML = '<i class="inline-icon-error"></i>'
            }

            saveBtn.classList.add('inactive')
            self.btns['revert'].classList.add('inactive')
            self.changes = {}
            self.backup()
        }

        xhr.send(JSON.stringify(this.changes))
    }

    public revertAll() {
        this.editablesForeach((el) => el.innerHTML = el.getAttribute('data-inline-backup'))
        this.btns['save'].classList.add('inactive')
        this.btns['revert'].classList.add('inactive')
        this.changes = {}
    }

    public enable() {
        this.btns['enable'].classList.add('inline-hidden')
        this.btns['disable'].classList.remove('inline-hidden')
        this.btns['save'].classList.remove('inline-hidden')
        this.btns['revert'].classList.remove('inline-hidden')

        this.editablesForeach((el) => el.classList.remove('inline-disabled'))

        let self = this
        for (let optionsName in this.editableConfigs) {

            let settings = (<any>Object).assign({
                entity_encoding: 'raw',
                inline: true,
                menubar: false,
                language: 'cs',
                plugins: 'paste link image lists nonbreaking',
                paste_as_text: true,
                theme: 'modern',
                setup: function (editor: Editor) {
                    editor.on('keyup change redo undo', () => self.updateContent(editor))
                }
            }, this.editableConfigs[optionsName])

            tinymce.init(settings)
        }
    }

    public disable() {
        this.btns['disable'].classList.add('inline-hidden')
        this.btns['status'].classList.add('inline-hidden')
        this.btns['save'].classList.add('inline-hidden')
        this.btns['revert'].classList.add('inline-hidden')
        this.btns['enable'].classList.remove('inline-hidden')

        this.editablesForeach((el) => {
            el.classList.add('inline-disabled')
        })

        tinymce.remove()
    }

    public backup() {
        this.editablesForeach((el) => el.setAttribute('data-inline-backup', el.innerHTML))
    }

    private editablesForeach(callback: (el: Element) => void) {
        for (let i = 0; i < this.editables.length; i++) {
            callback(this.editables[i])
        }
    }
}

interface BaseItem {
    content: string
}

class SimpleItem implements BaseItem {
    public type = 'simple'
    public constructor(public namespace: string, public locale: string, public name: string, public content: string) {
    }
}

class EntityItem implements BaseItem {
    public type = 'entity'
    public constructor(public entity: string, public id: string, public property: string, public content: string) {
    }
}

document.addEventListener('DOMContentLoaded', function () {
    let inline = new Inline()
});

