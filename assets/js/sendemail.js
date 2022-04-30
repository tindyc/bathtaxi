// listen to the form submission
document
  .getElementById("myForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const serviceID = "service_l30q5ea";
    const templateID = "contact_form";

    // send the email here
    emailjs.sendForm(serviceID, templateID, this).then(
      (response) => {
        console.log("SUCCESS!", response.status, response.text);
        alert("Thank you! We've recieved your request. We will get back to you as soon as possible.");
      },
      (error) => {
        console.log("FAILED...", error);
        alert("Oh no! Something bad happened! Please contact us via our number or email. Thank you", error);
      }
    );
  });