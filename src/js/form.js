document.addEventListener('DOMContentLoaded', function (e) {
    let memberForm = document.getElementById('tn_academy-member-form');
    let thanksText = document.getElementById('js-thanks');

    memberForm.addEventListener('submit', (e) => {
        e.preventDefault();

        // reset the form messages
        resetMessages();

        // collect all the data
        let data = {
            firstName: memberForm.querySelector('[name="first_name"]').value,
            lastName: memberForm.querySelector('[name="last_name"]').value,
            address: memberForm.querySelector('[name="address"]').value,
            zip: memberForm.querySelector('[name="zip"]').value,
            city: memberForm.querySelector('[name="city"]').value,
            phone: memberForm.querySelector('[name="phone"]').value,
            email: memberForm.querySelector('[name="email"]').value,
            message: memberForm.querySelector('[name="message"]').value,
            nonce: memberForm.querySelector('[name="nonce"]').value
        }

        // validate everything
        if (!data.firstName) {
            memberForm.querySelector('[data-error="invalidFirstName"]').classList.add('show');
            return;
        }

        if (!data.lastName) {
            memberForm.querySelector('[data-error="invalidLastName"]').classList.add('show');
            return;
        }

        if (!data.address) {
            memberForm.querySelector('[data-error="invalidAddress"]').classList.add('show');
            return;
        }

        if (!data.zip) {
            memberForm.querySelector('[data-error="invalidZip"]').classList.add('show');
            return;
        }

        if (!data.city) {
            memberForm.querySelector('[data-error="invalidCity"]').classList.add('show');
            return;
        }

        if (!data.phone) {
            memberForm.querySelector('[data-error="invalidPhone"]').classList.add('show');
            return;
        }

        if (!validateEmail(data.email)) {
            memberForm.querySelector('[data-error="invalidEmail"]').classList.add('show');
            return;
        }

        if (!data.message) {
            memberForm.querySelector('[data-error="invalidMessage"]').classList.add('show');
            return;
        }
        // ajax http post request
        let url = memberForm.dataset.url;
        let params = new URLSearchParams(new FormData(memberForm));

        memberForm.querySelector('.js-form-submission').classList.add('show');

        fetch(url, {
            method: "POST",
            body: params
        }).then(res => res.json()
        ).catch(error => {
            resetMessages();
            memberForm.querySelector('.js-form-error').classList.add('show');
        }).then(response => {
            resetMessages();

            if (response === 0 || response.status === 'error') {
                memberForm.querySelector('.js-form-error').classList.add('show');
                return;
            }
            memberForm.querySelector('.js-form-success').classList.add('show');

            memberForm.classList.add('tn_hide');
            thanksText.classList.remove('tn_hide');


            memberForm.reset();
        });
    });
});

function resetMessages() {
    document.querySelectorAll('.field-msg').forEach(f => f.classList.remove('show'));
}

function validateEmail(email) {
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}