// включаем интструменты разработчика Vue
Vue.config.debug = true
Vue.config.devtools = true

// функция для глубокого клонирования объектов
objectClone = function objectClone(obj) {
  return JSON.parse(JSON.stringify(obj))
}

// когда DOM загружен
document.addEventListener('DOMContentLoaded', () => {
  // удаление уведомлений по нажатию на кнопку .delete
  (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
    $notification = $delete.parentNode;

    $delete.addEventListener('click', () => {
      $notification.parentNode.removeChild($notification);
    });
  });
  
  // маска для телефона (Россия)
  $(".ivan-calc input[name=phone]").inputmask("+7 (999) 999-99-99", {
    "placeholder": "_"
  });
});

// класс заказа
Order = class Order {
  // конструктор
  constructor(options) {
    // конфигурация
    this.configuration = null
    // изображение
    this.image = null
    // тип дома
    this.houseType = null
    // профиль
    this.profile = null
    // стеклопакет
    this.glassPackage = null
    // цвет
    this.color = null

    // откосы
    this.slopes = false
    // подоконник
    this.sill = false
    // отливы
    this.apron = false
    // москитная сетка
    this.mosquitoNet = false
    // микропроветривание
    this.microVentilation = false
    // монтаж
    this.mounting = false

    // итого
    this.total = 0
  }

  // установка конфигурации
  setConfiguration(optConfiguration) {
    this.configuration = objectClone(optConfiguration)
    // обновляем изображение
    this.updateImage()
  }

  // обновление изображения в соответствии с параметрами
  updateImage() {
    // генерируем название изображения в зависимости от типа открытия створок
    let imageName = Object.values(this.configuration.sashes).reduce((imageName, sash) => {
      switch (sash.openingType.name) {
        case "Глухая":
          return imageName + "0"
        case "Поворотная":
          return imageName + "1"
        case "Поворотно-откидная":
          return imageName + "2"
        default:
          return imageName + "0"
      }
    }, "")

    this.image = this.configuration.images[imageName]
  }

  // название заказа
  getName() {
    let tmpName = this.configuration.name

    // добавляем к названию размеры
    switch (this.configuration.type) {
      case 'window':
        tmpName += " (" + this.configuration.windowWidth + "x" + this.configuration.windowHeight + ")"
        break
      case 'balcony':
        tmpName += " (" + this.configuration.doorWidth + "x" + this.configuration.doorHeight + ", " + this.configuration.windowWidth + "x" + this.configuration.windowHeight + ")"
        break
    }

    return tmpName
  }

  // итого
  getTotal() {
    let tmpTotal = 0

    if (this.configuration.type === "window") {
      // общая площадь изделия
      let area = this.configuration.windowWidth * this.configuration.windowHeight / (1000 * 1000)

      // рама
      let frame = (this.configuration.windowWidth + this.configuration.windowHeight) * 2 / 1000
      tmpTotal += frame * this.profile.windowFramePrice

      // импосты
      let nImpost = this.configuration.sashesCount - 1
      tmpTotal += ((this.configuration.windowHeight - 100) / 1000 * nImpost * this.profile.windowImpostPrice)

      // створки
      let sashWidth = this.configuration.windowWidth
      sashWidth -= 70 * 2 + 21 * 2 * nImpost
      sashWidth /= this.configuration.sashesCount
      let sashHeight = this.configuration.windowHeight - 70 * 2
      let sash = (sashWidth + sashHeight) * 2
      tmpTotal += sash / 1000 * this.configuration.sashesCount * this.profile.windowSashPrice

      // стеклопакет
      let glassPackageWidth = sashWidth - 10 * 2
      let glassPackageHeight = sashHeight - 10 * 2
      let glassPackageArea = glassPackageWidth * glassPackageHeight
      let glassPackagesArea = glassPackageArea * this.configuration.sashesCount
      tmpTotal += glassPackagesArea / (1000 * 1000) * this.glassPackage.price

      // фурнитура (типы открывания створок)
      Object.values(this.configuration.sashes).forEach(sash => {
        tmpTotal += sash.openingType.price
      })

      // цвет
      tmpTotal *= (100 + this.color.percent) / 100

      // откосы
      if (this.slopes === true) {
        let slopes = (this.configuration.windowWidth + this.configuration.windowHeight * 2) / 1000
        let slopesPrice = app.settings.slopes.reduce((price, x) => {
          if (x.forId === this.houseType.id) {
            return x.price
          } else {
            return price
          }
        }, 0)
        tmpTotal += slopes * slopesPrice
      }

      // подоконник
      if (this.sill === true) {
        let sill = this.configuration.windowWidth / 1000
        let sillPrice = app.settings.sill.reduce((price, x) => {
          if (x.forId === this.houseType.id) {
            return x.price
          } else {
            return price
          }
        }, 0)
        tmpTotal += sill * sillPrice
      }

      // отливы
      if (this.apron === true) {
        let apron = this.configuration.windowWidth / 1000
        let apronPrice = app.settings.apron.reduce((price, x) => {
          if (x.forId === this.houseType.id) {
            return x.price
          } else {
            return price
          }
        }, 0)
        tmpTotal += apron * apronPrice
      }

      // москитная сетка
      if (this.mosquitoNet === true) {
        let apronArea = glassPackagesArea / (1000 * 1000)
        let apronPrice = app.settings.apron.reduce((price, x) => {
          if (x.forId === this.houseType.id) {
            return x.price
          } else {
            return price
          }
        }, 0)
        Object.values(this.configuration.sashes).forEach(sash => {
          if (sash.openingType.name !== "Глухая") {
            tmpTotal += apronArea * apronPrice
          }
        })
      }

      // микропроветривание
      if (this.microVentilation === true) {
        Object.values(this.configuration.sashes).forEach(sash => {
          if (sash.openingType.name !== "Глухая") {
            tmpTotal += app.settings.microVentilationPrice
          }
        })
      }

      // монтаж
      if (this.mounting === true) {
        tmpTotal += area * app.settings.mountingPrice
      }
    } else if (this.configuration.type === "balcony") {
      // общая площадь изделия
      let area = (this.configuration.windowWidth * this.configuration.windowHeight + this.configuration.doorWidth * this.configuration.doorHeight) / (1000 * 1000)

      // рама
      let frame = ((this.configuration.windowWidth + this.configuration.windowHeight) * 2) / 1000
      tmpTotal += frame * this.profile.windowFramePrice

      // импосты
      let nImpost = this.configuration.sashesCount - 1
      tmpTotal += ((this.configuration.windowHeight - 100) / 1000 * nImpost * this.profile.windowImpostPrice)

      // створки
      let sashWidth = this.configuration.windowWidth
      sashWidth -= 70 * 2 + 21 * 2 * nImpost
      sashWidth /= this.configuration.sashesCount
      let sashHeight = this.configuration.windowHeight - 70 * 2
      let sash = (sashWidth + sashHeight) * 2
      tmpTotal += sash / 1000 * this.configuration.sashesCount * this.profile.windowSashPrice

      // стеклопакет
      let glassPackageWidth = sashWidth - 10 * 2
      let glassPackageHeight = sashHeight - 10 * 2
      let glassPackageArea = glassPackageWidth * glassPackageHeight
      let glassPackagesArea = glassPackageArea * this.configuration.sashesCount
      tmpTotal += glassPackagesArea / (1000 * 1000) * this.glassPackage.price

      // фурнитура (типы открывания створок)
      Object.values(this.configuration.sashes).forEach(sash => {
        tmpTotal += sash.openingType.price
      })

      // дверь
      // рама двери
      let doorFrame = ((this.configuration.doorWidth + this.configuration.doorHeight) * 2) / 1000
      let doorFramePrice = doorFrame * this.profile.windowFramePrice
      // створка двери
      let doorSashWidth = this.configuration.doorWidth - 70 * 2
      let doorSashHeight = this.configuration.doorHeight - 70 * 2
      let doorSash = (doorSashWidth + doorSashHeight) * 2
      let doorSashPrice = doorSash / 1000 * this.profile.windowSashPrice
      // стеклопакет двери
      let doorGlassPackageWidth = doorSashWidth - 10 * 2
      let doorGlassPackageHeight = doorSashHeight - 10 * 2
      let doorGlassPackageArea = doorGlassPackageWidth * doorGlassPackageHeight
      let doorGlassPackagePrice = doorGlassPackageArea / (1000 * 1000) * this.glassPackage.price
      // поворотно-откидная фурнитура двери
      let openingTypeSwingOutPrice = app.settings.openingTypes.reduce((price, x) => {
        if (x.name === "Поворотно-откидная") {
          return x.price
        } else {
          return price
        }
      }, 0)
      tmpTotal += doorFramePrice + doorSashPrice + doorGlassPackagePrice + openingTypeSwingOutPrice

      // цвет
      tmpTotal *= (100 + this.color.percent) / 100

      // откосы
      if (this.slopes === true) {
        let slopes = (this.configuration.windowWidth + this.configuration.doorWidth + this.configuration.windowHeight + this.configuration.doorHeight) / 1000
        let slopesPrice = app.settings.slopes.reduce((price, x) => {
          if (x.forId === this.houseType.id) {
            return x.price
          } else {
            return price
          }
        }, 0)
        tmpTotal += slopes * slopesPrice
      }

      // подоконник
      if (this.sill === true) {
        let sill = this.configuration.windowWidth / 1000
        let sillPrice = app.settings.sill.reduce((price, x) => {
          if (x.forId === this.houseType.id) {
            return x.price
          } else {
            return price
          }
        }, 0)
        tmpTotal += sill * sillPrice
      }

      // отливы
      if (this.apron === true) {
        let apron = this.configuration.windowWidth / 1000
        let apronPrice = app.settings.apron.reduce((price, x) => {
          if (x.forId === this.houseType.id) {
            return x.price
          } else {
            return price
          }
        }, 0)
        tmpTotal += apron * apronPrice
      }

      // москитная сетка
      if (this.mosquitoNet === true) {
        let apronArea = glassPackagesArea / (1000 * 1000)
        let apronPrice = app.settings.apron.reduce((price, x) => {
          if (x.forId === this.houseType.id) {
            return x.price
          } else {
            return price
          }
        }, 0)
        Object.values(this.configuration.sashes).forEach(sash => {
          if (sash.openingType.name !== "Глухая") {
            tmpTotal += apronArea * apronPrice
          }
        })
      }

      // микропроветривание
      if (this.microVentilation === true) {
        Object.values(this.configuration.sashes).forEach(sash => {
          if (sash.openingType.name !== "Глухая") {
            tmpTotal += app.settings.microVentilationPrice
          }
        })
      }

      // монтаж
      if (this.mounting === true) {
        tmpTotal += area * app.settings.mountingPrice
      }
    }

    // применяем скидку
    tmpTotal = tmpTotal * (100 - app.settings.discount) / 100

    // округляем до копеек в большую сторону
    tmpTotal = Math.ceil(tmpTotal * 100) / 100

    this.total = tmpTotal

    return tmpTotal
  }
}

app = new Vue({
  el: "#calc",
  data: {
    ready: false,
    settings: null,
    orders: [],
    currentOrder: null
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
      this.$http.get('./assets/settings.json', {
        'responseType': 'json'
      }).then(response => {
        this.settings = response.body

        this.settings.configurations = this.settings.configurations.map(el => {
          // добавляем объекты по количеству створок для хранения выбранного способа открывания
          el.sashes = {}
          for (i = 0; i < el.sashesCount; i++) {
            el.sashes[i] = {"openingType": this.settings.openingTypes[0]}
          }

          // добавляем размеры
          if (el.type === "window") {
            el.windowWidth = 1000
            el.windowHeight = 1000
          } else if (el.type === "balcony") {
            el.windowWidth = 1000
            el.windowHeight = 1000
            el.doorWidth = 1000
            el.doorHeight = 1000
          }

          return el
        }, this)

        // создаем первый заказ
        this.addOrder()
      }, response => {
        //console.log('Ошибка в loadSettings()')
      }).then(response => {
        // указываем, что объект готов к работе
        this.ready = true
      });
    },
    // добавление заказа
    addOrder: function () {
      // если заказов нет
      if (this.currentOrder === null) {
        // создаем новый заказ
        this.currentOrder = this.orders[this.orders.push(new Order()) - 1]

        // конфигурация
        this.currentOrder.setConfiguration(this.settings.configurations[0])
        // тип дома
        this.currentOrder.houseType = objectClone(this.settings.houseTypes[0])
        // профиль
        this.currentOrder.profile = objectClone(this.settings.profiles[0])
        // стеклопакет
        this.currentOrder.glassPackage = objectClone(this.settings.glassPackages[0])
        // цвет
        this.currentOrder.color = objectClone(this.settings.colors[0])
      } else {
        // создаем временный заказ
        let tmpOrder = this.orders[this.orders.push(new Order()) - 1]

        // конфигурация
        tmpOrder.setConfiguration(this.settings.configurations[0])

        // копируем параметры из текущего заказа
        // тип дома
        tmpOrder.houseType = objectClone(this.currentOrder.houseType)
        // профиль
        tmpOrder.profile = objectClone(this.currentOrder.profile)
        // стеклопакет
        tmpOrder.glassPackage = objectClone(this.currentOrder.glassPackage)
        // цвет
        tmpOrder.color = objectClone(this.currentOrder.color)

        // москитная сетка
        tmpOrder.mosquitoNet = this.currentOrder.mosquitoNet
        // откосы
        tmpOrder.slopes = this.currentOrder.slopes
        // подоконник
        tmpOrder.sill = this.currentOrder.sill
        // отливы
        tmpOrder.apron = this.currentOrder.apron
        // микропроветривание
        tmpOrder.microVentilation = this.currentOrder.microVentilation
        // монтаж
        tmpOrder.mounting = this.currentOrder.mounting

        // устанавливаем добавленный заказ текущим
        this.currentOrder = tmpOrder
      }
    },
    // удаление заказа
    deleteOrder: function (optIOrder) {
      // сохраняем индекс текущего заказа в массиве
      iCurrentOrder = (this.orders.indexOf(this.currentOrder) != -1) ? this.orders.indexOf(this.currentOrder) : 0
      // удаляем заказ
      this.orders.splice(optIOrder, 1)
      // устанавливаем текущий заказ в соотвествии с измененным массивом
      this.currentOrder = this.orders[iCurrentOrder === 0 ? iCurrentOrder : iCurrentOrder - 1]
    },
    // отправка данных на почту
    send: function () {
      $(".ivan-calc .notification")

      if ($(".ivan-calc input[name=name]").val() == "") {
        $(".ivan-calc input[name=name]").addClass("is-danger");
        $(".ivan-calc input[name=name]").focus();
        return false;
      } else {
        $(".ivan-calc input[name=name]").removeClass("is-danger");
        $(".ivan-calc input[name=name]").addClass("is-success");
      }
      if (!$(".ivan-calc input[name=phone]").inputmask("isComplete")) {
        $(".ivan-calc input[name=phone]").addClass("is-danger");
        $(".ivan-calc input[name=phone]").focus();
        return false;
      } else {
        $(".ivan-calc input[name=phone]").removeClass("is-danger");
        $(".ivan-calc input[name=phone]").addClass("is-success");
      }

      var sendData = {
        'orders': this.orders,
        'clientName': $("input[name=name]").val(),
        'clientPhone': $("input[name=phone]").val(),
        'total': this.total
      }

      that = this

      $.ajax({
        method: "POST",
        url: "./assets/php/send.php",
        data: sendData
      }).done(function (msg) {
        console.log(msg);
        if (msg == true) {
          $(".c__total").append('<div class="column is-full"><div class="notification is-success is-light"><button class="delete"></button>Ваша заявка успешно отправлена. Спасибо!</div></div>');
        } else {
          $(".c__total").append('<div class="column is-full"><div class="notification is-danger is-light"><button class="delete"></button>Произошла ошибка. Позвоните нам: <a href="tel:' + that.settings.companyPhone.replace(/[^0-9]/gim, "") + '">' + that.settings.companyPhone + '</a></div></div>');
        }
      });
    }
  },
  filters: {
    // форматированный вывод цены
    priceFormat: function (value) {
      if (!value) return ''
      return value.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')
    }
  },
  computed: {
    // итоговая стоимость всех заказов
    total: function () {
      let tmpTotal = this.orders.reduce((sum, order) => {
        return sum + order.getTotal()
      }, 0)
      tmpTotal = Math.round(tmpTotal * 100) / 100
      return tmpTotal
    }
  }
})