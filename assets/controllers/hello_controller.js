import { Controller } from "@hotwired/stimulus";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
  connect() {
    Controller.register(
      "images",
      ImagesController
    );
    Controller.register(
      "order_paiement_controller",
      OrderPaiementController
    );
    // this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';
  }
}
