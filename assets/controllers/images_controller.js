import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect() {
    let links = document.querySelectorAll(
      "[data-delete]"
    );
    //boucle sur les liens
    for (let link of links) {
      // ecouter event
      link.addEventListener(
        "click",
        function (e) {
          //empecher navigation
          e.preventDefault();
          //demander confirmation
          if (
            confirm(
              "Voulez-vous vraiment supprimer cette image ?"
            )
          ) {
            //envoie requete ajax
            fetch(this.getAttribute("href"), {
              method: "DELETE",
              headers: {
                "X-Requested-With":
                  "XMLHttpRequest",
                "Content-Type":
                  "application/json",
              },
              body: JSON.stringify({
                _token: this.dataset.token,
              }),
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  this.parentElement.remove();
                } else {
                  alert(data.error);
                }
              });
          }
        }
      );
    }
  }
}
