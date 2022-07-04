$(document).ready(function () {
  // событие отправки формы
  $(".ivan-calc form.form").submit(function (event) {
    event.preventDefault();

    let password = $(".ivan-calc form.form input[name=password]").val();

    $(".ivan-calc .notification").parent().remove();

    if (password == "") {
      $(".ivan-calc form.form input[name=password]").addClass("is-danger");
      $(".ivan-calc form.form input[name=password]").focus();
      return false;
    } else {
      $(".ivan-calc form.form input[name=password]").removeClass("is-danger");

      $.ajax({
        method: "POST",
        url: "./php/login.php",
        data: {"password": password}
      }).done(function (msg) {
        console.log(msg);
        if (msg == true) {
          location.reload()
        } else {
          $("form.form").append('<div class="field"><div class="notification is-danger is-light">Неверный пароль. Попробуйте еще раз.</div></div>');
          $(".ivan-calc form.form input[name=password]").addClass("is-danger");
          $(".ivan-calc form.form input[name=password]").focus();
        }
      });
    }
  });
})