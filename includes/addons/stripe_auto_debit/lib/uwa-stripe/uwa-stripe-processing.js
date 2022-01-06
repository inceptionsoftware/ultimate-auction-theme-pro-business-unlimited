
var stripe = Stripe(stripe_vars.publishable_key);
var elements = stripe.elements();
  var elementStyles = {
    base: {
      color: '#32325D',
      fontWeight: 500,
      fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
      fontSize: '16px',
      fontSmoothing: 'antialiased',

      '::placeholder': {
        color: '#CFD7DF',
      },
      ':-webkit-autofill': {
        color: '#e39f48',
      },
    },
    invalid: {
      color: '#E25950',

      '::placeholder': {
        color: '#FFCCA5',
      },
    },
  };

  var elementClasses = {
    focus: 'focused',
    empty: 'empty',
    invalid: 'invalid',
  };

  var cardNumberElement = elements.create('cardNumber', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardNumberElement.mount('#uwa-card-number');

  var cardExpiryElement = elements.create('cardExpiry', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardExpiryElement.mount('#uwa-card-expiry');

  var cardCvcElement = elements.create('cardCvc', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardCvcElement.mount('#uwa-card-cvc');


function stripeSourceHandler(source) {
  // Insert the source ID into the form so it gets submitted to the server
  var form = jQuery(".woocommerce-form-register");  
  jQuery("#uwa_stripe_k_id").val(source.id);
	jQuery("#terms").attr("checked", "checked");
	
}

jQuery(document).ready(function($) {
	
	$("#terms").click(function(event) {		
		
		var chkvalue = $(this).attr("checked");
		if(chkvalue == "checked"){					
			$("#terms").attr("checked", false);
		}		
		var chkvalue = $(this).attr("checked");
		var ownerInfo = {
		  owner: {			
			email: document.getElementById('reg_email')
		  },
		};
		
		stripe.createSource(cardNumberElement, ownerInfo).then(function(result) {
				if (result.error) {
				  // Inform the user if there was an error
				  var errorElement = document.getElementById('uwa-card-errors');
				  errorElement.textContent = result.error.message;
				} else {
				  // Send the source to your server
				  stripeSourceHandler(result.source);
				}
			  });
				
				
	});

cardNumberElement.addEventListener('change', function(event) {
    validate_inputs();	
});
cardExpiryElement.addEventListener('change', function(event) {
    validate_inputs();
});
cardCvcElement.addEventListener('change', function(event) {
    validate_inputs();
});


	function validate_inputs() {
		// uncheck checkbox during validations
		var chkvalue = $("#terms").attr("checked");
		if(chkvalue == "checked"){					
			$("#terms").attr("checked", false);
		}
	}

});
