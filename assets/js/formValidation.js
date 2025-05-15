document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form#regForm");
  if (!form) return;

  const mezok = form.querySelectorAll("input");

  // Blur esemény
  mezok.forEach(input => {
    input.addEventListener("blur", () => {
      if (!input.value.trim()) {
        input.classList.add("input-error");
      } else {
        input.classList.remove("input-error");
      }
    });
  });

  // Submit esemény
  form.addEventListener("submit", (e) => {
    let elsoHibas = null;
    let vanHiba = false;

    mezok.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add("input-error");
        if (!elsoHibas) elsoHibas = input;
        vanHiba = true;
      } else {
        input.classList.remove("input-error");
      }
    });

    if (vanHiba) {
      e.preventDefault();
      elsoHibas.focus();
    }
  });
});