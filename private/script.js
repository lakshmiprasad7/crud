function addContact() {
    const contactFields = document.getElementById('contactFields');
    const newContact = document.createElement('div');

    newContact.innerHTML =
        '<div class="contact-inputs row">' +
        '<div class="col-md-6 mb-3">' +
        '<input type="tel" class="form-control" name="phone[]" pattern="[0-9]{10}" placeholder="Phone Number" required>' +
        '</div>' +
        '<div class="col-md-6 mb-3">' +
        '<input type="email" class="form-control" name="email[]" placeholder="Email" required>' +
        '</div>' +
        '</div>';

    contactFields.appendChild(newContact);
}

function removeContact() {
    const contactFields = document.getElementById('contactFields');
    const lastContact = contactFields.lastElementChild;

    if (lastContact) {
        contactFields.removeChild(lastContact);
    }
}

function setError(element, message) {
    element.innerHTML = message;
}

function clearError(element) {
    element.innerHTML = '';
}

async function validateField(value, fieldName, minLength) {
    const errorElement = document.getElementById(`${fieldName}ValidationError`);

    if (value.trim() === '') {
        setError(errorElement, `*Please enter ${fieldName}`);
        return false;
    } else if (value.length < minLength) {
        setError(errorElement, `*${fieldName} must contain at least ${minLength} characters`);
        return false;
    } else {
        clearError(errorElement);
        return true;
    }
}

async function validateForm(event) {
    document.getElementById('validationErrors').innerHTML = '';
    document.getElementById('generalValidationErrors').innerHTML = '';

    const indentName = document.getElementById('indentName').value;
    const indentorName = document.getElementById('indentorName').value;
    const supplierName = document.getElementById('supplierName').value;
    const poValue = document.getElementById('poValue').value;
    const status = document.getElementById('status').value;
    const pdStartDate = document.getElementById('pdStartDate').value;
    const pdEndDate = document.getElementById('pdEndDate').value;
    const phoneInputs = document.getElementsByName('phone[]');
    const emailInputs = document.getElementsByName('email[]');

    let isValid = true;

    // Validate fields
    isValid = isValid && (await validateField(indentName, 'indentName', 5));
    isValid = isValid && (await validateField(indentorName, 'indentorName', 5));
    isValid = isValid && (await validateField(supplierName, 'supplierName', 5));
    isValid = isValid && (await validateField(poValue, 'poValue', 1));
    isValid = isValid && (await validateField(status, 'status', 1));

    // Validate PD Start Date
    isValid = isValid && (await validateDate(pdStartDate, 'pdStartDateError'));

    // Validate PD End Date
    isValid = isValid && (await validateDate(pdEndDate, 'pdEndDateError'));

    // Validate Contact Details
    isValid = isValid && (await validateContactDetails(phoneInputs, 'phone'));
    isValid = isValid && (await validateContactDetails(emailInputs, 'email'));

    if (!isValid) {
        document.getElementById('validationErrors').innerHTML = 'Please fill in all details.';
        if (event) {
            event.preventDefault();
        }
    } else {
        // Your form submission logic goes here
        console.log('Form submitted successfully!');
        // In a real scenario, you might want to perform an AJAX request or other actions
    }
}

async function validateDate(dateString, errorElementId) {
    const errorElement = document.getElementById(errorElementId);

    if (!dateString.trim()) {
        setError(errorElement, '*Please select a date.');
        return false;
    }

    const selectedDate = new Date(dateString);

    if (selectedDate < getTodaysDate()) {
        setError(errorElement, '*Please select a date equal to or after today.');
        return false;
    } else {
        clearError(errorElement);
        return true;
    }
}

async function validateContactDetails(inputs, fieldName) {
    const validationErrors = document.getElementById('contactValidationErrors');
    validationErrors.innerHTML = '';

    for (const input of inputs) {
        if (!(await validateField(input.value, fieldName, fieldName === 'phone' ? 10 : 1))) {
            return false;
        }
    }

    return true;
}

function getTodaysDate() {
    return new Date();
}

document.getElementById('pdForm').addEventListener('submit', function (event) {
    validateForm(event);
});
