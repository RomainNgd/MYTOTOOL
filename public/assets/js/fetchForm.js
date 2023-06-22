import { textFetch } from './api'
import { setupDatePicker } from './dateTimePicker'
import { setupSelect2 } from './select2'

export function fetchForm(btnSelector, callbacks = []) {
    const elements = document.querySelectorAll(btnSelector)
    elements.forEach(function (element) {
        element.addEventListener('click', function (event) {
            showFormLoader()
            const url = event.currentTarget.href
            textFetch(url).then((data) => {
                // load html form
                const formContentSelectorFetch = document.querySelector('#form-content')
                formContentSelectorFetch.innerHTML = data
                setupDatePicker('.datetime-picker')
                setupSelect2('.select2-demo')
                callbacks.forEach((callback) => {
                    if (callback instanceof Function) {
                        callback()
                    }
                })

                const formElementFetch = formContentSelectorFetch.querySelector('form')
                if (!formElementFetch) {
                    console.warn("Le formulaire n'a pas été trouvé!")
                    return
                }

                formElementFetch.addEventListener('submit', function (e) {
                    e.preventDefault()

                    textFetch(url, {
                        method: 'post',
                        body: new FormData(e.currentTarget),
                    })
                        .then(() => {
                            location.reload()
                        })
                        .catch((error) => {
                            let content = error.cause.content
                            content = JSON.parse(content)
                            if (typeof content === 'string') {
                                printViolation([content])
                            } else if (typeof content === 'object') {
                                printViolation(content)
                            } else {
                                printViolation(['Une erreur est survenue!'])
                            }
                        })
                })
            })
        })
    })
}

/**
 * @param messages {string[]}
 */
export function printViolation(messages) {
    let content = ''
    messages.forEach((message) => {
        content += '<div class="alert alert-dark-danger mx-2"> ' + message + ' </div>'
    })
    document.getElementById('flash-content').innerHTML = content
    document.getElementById('modal-header').scrollIntoView({ behavior: 'smooth' })
}

/**
 * Add loader for form-content
 */
export function showFormLoader() {
    document.getElementById('form-content').innerHTML = '<div class="spinner-border text-primary" role="status"> </div>'
}
