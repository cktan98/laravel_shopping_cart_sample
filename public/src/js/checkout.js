// var stripe = Stripe('pk_test_51GtPlTARuhT4kw25rWXnA3waH6eyveMp6jATG6nIrrsvxKSiVvGLMyl4Hft3cXuw7YbABoqkCzaMRXDbkqoAEujj007vKWNGxm');
// var elements = stripe.elements();

// var card = elements.create('card', {
//     hidePostalCode: true,
//     'style': {
//       'base': {
//         'fontFamily': 'Arial, sans-serif',
//         'fontSize': '16px',
//         'color': '#C1C7CD',
//       },
//       'invalid': {
//         'color': 'red',
//       },
//     }
//   });

// card.mount('#card-element');


// var form = document.getElementById('checkout-form');
// form.addEventListener('submit', function(e) {
//   e.preventDefault();
//   createToken();
// });

// function createToken() {
//     stripe.createToken(card).then(function(result) {
//         if (result.error) {
//         // Inform the user if there was an error
//         var errorElement = document.getElementById('card-errors');
//         errorElement.textContent = result.error.message;
//         } else {
//         // Send the token to your server
//         stripeTokenHandler(result.token);
//         }
//     });
// };

// function stripeTokenHandler(token) {
//     // Insert the token ID into the form so it gets submitted to the server
//     // var form = document.getElementById('checkout-form');
//     var hiddenInput = document.createElement('input');
//     hiddenInput.setAttribute('type', 'hidden');
//     hiddenInput.setAttribute('name', 'stripeToken');
//     hiddenInput.setAttribute('value', token.id);
//     form.appendChild(hiddenInput);

//     // Submit the form
//     form.submit();
// }
Stripe.setPublishableKey('pk_test_51GtPlTARuhT4kw25rWXnA3waH6eyveMp6jATG6nIrrsvxKSiVvGLMyl4Hft3cXuw7YbABoqkCzaMRXDbkqoAEujj007vKWNGxm');

var $form = $('#checkout-form');

$form.submit(function(event) {
    $('#charge-error').addClass('hidden');
    $form.find('button').prop('disabled', true);
    Stripe.card.createToken({
        number: $('#card-number').val(),
        cvc: $('#card-cvc').val(),
        exp_month: $('#card-expiry-month').val(),
        exp_year: $('#card-expiry-year').val(),
        name: $('#card-name').val()
    }, stripeResponseHandler);
    return false;
});

function stripeResponseHandler(status, response) {
    if (response.error) {
        $('#charge-error').removeClass('hidden');
        $('#charge-error').text(response.error.message);
        $form.find('button').prop('disabled', false);
    } else {
        var token = response.id;
        $form.append($('<input type="hidden" name="stripeToken" />').val(token));

        // Submit the form:
        $form.get(0).submit();
    }
}


