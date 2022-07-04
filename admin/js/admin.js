// включаем интструменты разработчика Vue
Vue.config.debug = true
Vue.config.devtools = true

app = new Vue({
  el: "#calc_admin",
  data: {
    ready: false,
    settings: null,
    pages: [
      {
        "name": "Конфигурации",
        "type": "tab"
      },
      {
        "name": "Типы домов",
        "type": "tab"
      },
      {
        "name": "Профили",
        "type": "tab"
      },
      {
        "name": "Стеклопакеты",
        "type": "tab"
      },
      {
        "name": "Типы створок",
        "type": "tab"
      },
      {
        "name": "Цвета",
        "type": "tab"
      },
      {
        "name": "Откосы",
        "type": "tab"
      },
      {
        "name": "Подоконник",
        "type": "tab"
      },
      {
        "name": "Отливы",
        "type": "tab"
      },
      {
        "name": "Дополнительные параметры",
        "type": "tab"
      }
    ],
    currentPage: 0,
    password: null,
    password2: null
  },
  // хук создания экземпляра Vue
  created: function () {
    //console.log("created")
    this.loadSettings()
  },
  methods: {
    // загрузка БД
    loadSettings() {
      //console.log("loadSettings")
      this.$http.get('../assets/settings.json', {
        'responseType': 'json'
      }).then(response => {
        this.settings = response.body
      }, response => {
        console.log('Ошибка в loadSettings()')
      }).then(response => {
        // указываем, что объект готов к работе
        this.ready = true
      });
    },
    // показать страницу
    showPage: function (optIPage) {
      this.currentPage = optIPage
    },
    // выход из админки
    logout: function () {
      window.location = "./php/logout.php"
    },
    // удаление типа дома
    deleteHouseType: function (optIHouseType) {
      let elHouseType = this.settings.houseTypes[optIHouseType]

      if (confirm('Удалить тип дома \'' + elHouseType.name + '\'?')) {
        // удаляем тип дома из откосов
        this.settings.slopes.splice(this.settings.slopes.reduce((a, x, i) => {
          if (x.forId === elHouseType.id) {
            return i;
          } else {
            return a;
          }
        }, false), 1)

        // удаляем тип дома из подоконника
        this.settings.sill.splice(this.settings.sill.reduce((a, x, i) => {
          if (x.forId === elHouseType.id) {
            return i;
          } else {
            return a;
          }
        }, false), 1)

        // удаляем тип дома из отливов
        this.settings.apron.splice(this.settings.apron.reduce((a, x, i) => {
          if (x.forId === elHouseType.id) {
            return i;
          } else {
            return a;
          }
        }, false), 1)

        this.settings.houseTypes.splice(optIHouseType, 1);
      }
    },
    // добавление типа дома
    addHouseType: function () {
      newId = 1 + this.settings.houseTypes.reduce((a, x) => {
        if (x.id >= a) {
          return x.id
        }
      }, 0)
      this.settings.houseTypes.push({'id': newId, 'name': ''})

      // добавляем тип дома в откосы
      this.settings.slopes.push({
        'forId': newId,
        'price': 0
      },)

      // добавляем тип дома в подоконник
      this.settings.sill.push({
        'forId': newId,
        'price': 0
      },)

      // добавляем тип дома в отливы
      this.settings.apron.push({
        'forId': newId,
        'price': 0
      },)

    },
    // сохранение изменений
    save: function () {
      that = this

      $.ajax({
        method: "POST",
        url: "./php/admin.php?action=save",
        data: {'settings': that.settings}
      }).done(function (msg) {
        console.log(msg);
        if (msg == true) {
          alert("Изменения успешно сохранены")
        } else {
          alert("Ошибка! Не удалось сохранить изменения.")
        }
      });
    },
    // обновление пароля
    changePassword: function () {
      if (this.password) {
        if (this.password === this.password2) {
          that = this

          $.ajax({
            method: "POST",
            url: "./php/admin.php?action=changePassword",
            data: {'password': that.password}
          }).done(function (msg) {
            console.log(msg);
            if (msg == true) {
              alert("Новый пароль установлен")
            } else {
              alert("Не удалось изменить пароль!")
            }
          });
        } else {
          alert("Пароли не совпадают!")
        }
      } else {
        alert("Пароль не может быть пустым!");
      }
    }
  }
})