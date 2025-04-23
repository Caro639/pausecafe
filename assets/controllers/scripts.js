/*!
 * Start Bootstrap - Shop Homepage v5.0.6 (https://startbootstrap.com/template/shop-homepage)
 * Copyright 2013-2023 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-shop-homepage/blob/master/LICENSE)
 */
// This file is intentionally blank
// Use this file to add JavaScript to your project
// document.addEventListener(
//   "DOMContentLoaded",
//   function () {
//     const clientSecret = "{{ clientSecret }}";

//     // This is your test publishable API key.
//     const stripe = Stripe(
//       "pk_test_51NsjDZKAwtwLScn28yr6BJfzodjJG3Z5LL24KicPnd6MhaEHieL95A0AdyDTHLVU7lrurpXOS8ECZUZYeojzYe9J00IpsGHrQM"
//     );
//     console.log("Stripe", stripe);
//     console.log("Client Secret:", clientSecret);
//     // Retrieve the PaymentIntent from the server
//     stripe
//       .retrievePaymentIntent(clientSecret)
//       .then(function (result) {
//         const paymentIntent =
//           result.paymentIntent;
//         console.log(
//           "paymentIntent:",
//           paymentIntent
//         );
//       });

//     const elements = stripe.elements();

//     const card = elements.create("card", {
//       style: {
//         base: {
//           iconColor: "#c4f0ff",
//           color: "#000",
//           fontWeight: "500",
//           fontFamily:
//             "Roboto, Open Sans, Segoe UI, sans-serif",
//           fontSize: "16px",
//           fontSmoothing: "antialiased",
//           ":-webkit-autofill": {
//             color: "#fce883",
//           },
//           "::placeholder": { color: "#87BBFD" },
//         },
//         invalid: {
//           iconColor: "#FFC7EE",
//           color: "#FFC7EE",
//         },
//       },
//     });

//     card.mount("#card-element");

//     document.addEventListener(
//       "DOMContentLoaded",
//       function () {
//         console.log(
//           "Card Element:",
//           document.getElementById("card-element")
//         );
//       }
//     );

//     card.on("change", function (event) {
//       const button =
//         document.querySelector("button");
//       button.disabled = event.empty;
//       const errorElement =
//         document.getElementById("card-errors");
//       errorElement.textContent = event.error
//         ? event.error.message
//         : "";
//     });

//     const form = document.getElementById(
//       "payment-form"
//     );

//     form.addEventListener(
//       "submit",
//       function (event) {
//         event.preventDefault();

//         stripe
//           .confirmCardPayment(clientSecret, {
//             payment_method: {
//               card: card,
//             },
//           })
//           .then(function (result) {
//             if (result.error) {
//               // Affichez l'erreur à l'utilisateur
//               const errorElement =
//                 document.getElementById(
//                   "card-errors"
//                 );
//               errorElement.textContent =
//                 result.error.message;
//             } else {
//               // Le paiement a réussi
//               console.log(
//                 "Paiement réussi :",
//                 result.paymentIntent.id
//               );
//               window.location.href =
//                 redirectAfterSuccessUrl;
//               // "{{ url('app_orders_detail', { id: order.id }) }}";
//               // Redirigez vers une page de succès
//             }
//           });
//       }
//     );
//   }
// );
