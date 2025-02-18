import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        addLabel: String,
        deleteLabel: String
    }

    connect() {
        this.index = this.element.childElementCount
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-secondary')
        btn.innerText = this.addLabelValue || 'Ajouter un élément'
        btn.setAttribute('type', 'button')
        btn.addEventListener('click', this.addElement)
        this.element.childNodes.forEach((item) => this.addDeleteButton(item))
        this.element.append(btn)
    }

    /**
     * Ajoute une nouvelle entrée dans la structure HTML
     *
     * @param {MouseEvent} e
     */
    addElement = (e) => {
        e.preventDefault()
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replaceAll('__name__', this.index)
        ).firstElementChild
        this.addDeleteButton(element);
        this.index++
        e.currentTarget.insertAdjacentElement('beforebegin', element)
    }

    /**
     *
     * @param {HTMLElement} item
     */
    addDeleteButton = (item) => {
        const $deleteBtn = document.createElement('button');
        $deleteBtn.setAttribute('class', 'btn btn-danger')
        $deleteBtn.setAttribute('type', 'button')
        $deleteBtn.innerText = this.deleteLabelValue || 'Supprimer'
        item.append($deleteBtn)
        $deleteBtn.addEventListener('click', (e) => {
            e.preventDefault()
            item.remove();
        })
    }
}
