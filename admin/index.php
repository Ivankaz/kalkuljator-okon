<?php
session_start();
if (isset($_SESSION['login'])) { ?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Настройки калькулятора стоимости пластиковых окон и балконов</title>
  <link rel="stylesheet" href="./css/bulma.min.css">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body class="ivan-calc">

<script src="./js/vue.js"></script>

<!-- Заголовок -->
<section class="hero is-dark is-light">
  <div class="hero-body">
    <div class="container">
      <h1 class="subtitle has-text-centered">
        Настройки калькулятора стоимости пластиковых окон и балконов
      </h1>
    </div>
  </div>
</section>

<div class="section" id="calc_admin" v-if="ready===true">
  <div class="container">

    <!-- Кнопки настроек -->
    <div class="columns">
      <div class="column">

        <!-- Кнопки управления -->
        <div class="buttons">
          <!-- Кнопка Почта -->
          <span class="button is-dark" @click="showPage('emails')">Почта</span>
          <!-- Кнопка Пароль -->
          <span class="button is-dark" @click="showPage('password')">Пароль</span>
          <!-- Кнопка Сохранить -->
          <span class="button is-success" @click="save()">Сохранить</span>
          <!-- Кнопка Выйти -->
          <span class="button is-danger" @click="logout()">Выйти</span>
        </div>

        <!-- Кнопки настроек -->
        <div class="buttons">
          <span v-for="(elPage, iPage) in pages"
                :class="[currentPage===iPage ? 'button is-link' : 'button is-link is-light']" @click="showPage(iPage)">
             {{ elPage.name }}
          </span>
        </div>
      </div>

      <!-- Страницы настроек -->
      <!-- Конфигурации -->
      <div v-show="currentPage===0" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elConfiguration, iConfiguration) in settings.configurations" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Название</label>
                <div class="control">
                  <input v-model="elConfiguration.name" class="input" type="text" placeholder="Название конфигурации">
                </div>
              </div>

              <!-- Тип -->
              <div class="field">
                <label class="label">Тип</label>
                <div class="control">
                  <div class="select is-fullwidth">
                    <select v-model="elConfiguration.type">
                      <option value="window">Окно</option>
                      <option value="balcony">Балкон</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Количество створок -->
              <div class="field">
                <label class="label">Количество створок</label>
                <div class="control">
                  <input v-model.number="elConfiguration.sashesCount" class="input" type="number"
                         placeholder="Количество створок">
                </div>
              </div>

            </div>

          </div>
        </div>
      </div>

      <!-- Типы домов -->
      <div v-show="currentPage===1" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elHouseType, iHouseType) in settings.houseTypes" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Название</label>
                <div class="control">
                  <input v-model="elHouseType.name" class="input" type="text" placeholder="Название типа дома">
                </div>
              </div>

              <!-- Кнопка Удалить -->
              <div class="field">
                <div class="control">
                  <button @click="deleteHouseType(iHouseType)"
                          class="button is-danger is-fullwidth">Удалить
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Кнопка Добавить -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <div class="control">
                  <button @click="addHouseType()" class="button is-success is-fullwidth">
                    Добавить
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Профили -->
      <div v-show="currentPage===2" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elProfile, iProfile) in settings.profiles" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Название</label>
                <div class="control">
                  <input v-model="elProfile.name" class="input" type="text" placeholder="Название профиля">
                </div>
              </div>

              <!-- Цена рамы за м.п. -->
              <div class="field">
                <label class="label">Цена рамы за м.п.</label>
                <div class="control">
                  <input v-model.number="elProfile.windowFramePrice" class="input" type="number"
                         placeholder="Цена рамы за м.п.">
                </div>
              </div>

              <!-- Цена створки за м.п. -->
              <div class="field">
                <label class="label">Цена створки за м.п.</label>
                <div class="control">
                  <input v-model.number="elProfile.windowSashPrice" class="input" type="number"
                         placeholder="Цена створки за м.п.">
                </div>
              </div>

              <!-- Цена импоста за м.п. -->
              <div class="field">
                <label class="label">Цена импоста за м.п.</label>
                <div class="control">
                  <input v-model.number="elProfile.windowImpostPrice" class="input" type="number"
                         placeholder="Цена импоста за м.п.">
                </div>
              </div>

              <!-- Кнопка Удалить -->
              <div class="field">
                <div class="control">
                  <button @click="confirm('Удалить профиль \''+elProfile.name+'\'?') ? settings.profiles.splice(iProfile, 1) : null"
                          class="button is-danger is-fullwidth">Удалить
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Кнопка Добавить -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <div class="control">
                  <button @click="settings.profiles.push({'name': '', 'windowFramePrice': 0, 'windowSashPrice': 0, 'windowImpostPrice': 0})"
                          class="button is-success is-fullwidth">
                    Добавить
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Стеклопакеты -->
      <div v-show="currentPage===3" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elGlassPackage, iGlassPackage) in settings.glassPackages" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Название</label>
                <div class="control">
                  <input v-model="elGlassPackage.name" class="input" type="text" placeholder="Название стеклопакета">
                </div>
              </div>

              <!-- Цена -->
              <div class="field">
                <label class="label">Цена за м<sup>2</sup></label>
                <div class="control">
                  <input v-model.number="elGlassPackage.price" class="input" type="number"
                         placeholder="Цена стеклопакета">
                </div>
              </div>

              <!-- Кнопка Удалить -->
              <div class="field">
                <div class="control">
                  <button @click="confirm('Удалить стеклопакет \''+elGlassPackage.name+'\'?') ? settings.glassPackages.splice(iGlassPackage, 1) : null"
                          class="button is-danger is-fullwidth">Удалить
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Кнопка Добавить -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <div class="control">
                  <button @click="settings.glassPackages.push({'name': '', 'price': 0})"
                          class="button is-success is-fullwidth">
                    Добавить
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Типы створок -->
      <div v-show="currentPage===4" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elOpeningType, iOpeningType) in settings.openingTypes" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Название</label>
                <div class="control">
                  <input v-model="elOpeningType.name" class="input" type="text" placeholder="Название типа створки">
                </div>
              </div>

              <!-- Цена -->
              <div class="field">
                <label class="label">Цена за шт.</label>
                <div class="control">
                  <input v-model.number="elOpeningType.price" class="input" type="number"
                         placeholder="Цена типа створки">
                </div>
              </div>

              <!-- Кнопка Удалить -->
              <div class="field">
                <div class="control">
                  <button @click="confirm('Удалить тип створки \''+elOpeningType.name+'\'?') ? settings.openingTypes.splice(iOpeningType, 1) : null"
                          class="button is-danger is-fullwidth">Удалить
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Кнопка Добавить -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <div class="control">
                  <button @click="settings.openingTypes.push({'name': '', 'price': 0})"
                          class="button is-success is-fullwidth">
                    Добавить
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Цвета -->
      <div v-show="currentPage===5" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elColor, iColor) in settings.colors" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Название</label>
                <div class="control">
                  <input v-model="elColor.name" class="input" type="text" placeholder="Название цвета">
                </div>
              </div>

              <!-- Цена -->
              <div class="field">
                <label class="label">Сколько добавлять процентов к стоимости изделия</label>
                <div class="control">
                  <input v-model.number="elColor.percent" class="input" type="number"
                         placeholder="Проценты">
                </div>
              </div>

              <!-- Кнопка Удалить -->
              <div class="field">
                <div class="control">
                  <button @click="confirm('Удалить цвет \''+elColor.name+'\'?') ? settings.colors.splice(iColor, 1) : null"
                          class="button is-danger is-fullwidth">Удалить
                  </button>
                </div>
              </div>

            </div>
          </div>

          <!-- Кнопка Добавить -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <div class="control">
                  <button @click="settings.colors.push({'name': '', 'percent': 0})"
                          class="button is-success is-fullwidth">
                    Добавить
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Откосы -->
      <div v-show="currentPage===6" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elSlope, iSlope) in settings.slopes" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Цена для типа дома "{{ settings.houseTypes.filter(x => x.id==elSlope.forId)[0].name
                  }}"</label>
                <div class="control">
                  <input v-model.number="elSlope.price" class="input" type="number" placeholder="Цена за откосы">
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- Подоконник -->
      <div v-show="currentPage===7" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elSill, iSill) in settings.sill" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Цена для типа дома "{{ settings.houseTypes.filter(x => x.id==elSill.forId)[0].name
                  }}"</label>
                <div class="control">
                  <input v-model.number="elSill.price" class="input" type="number" placeholder="Цена за подоконник">
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- Отливы -->
      <div v-show="currentPage===8" class="column">
        <div class="columns is-desktop is-multiline">
          <div v-for="(elApron, iApron) in settings.apron" class="column is-half-desktop">
            <div class="box">

              <!-- Название -->
              <div class="field">
                <label class="label">Цена для типа дома "{{ settings.houseTypes.filter(x => x.id==elApron.forId)[0].name
                  }}"</label>
                <div class="control">
                  <input v-model.number="elApron.price" class="input" type="number" placeholder="Цена за отливы">
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- Дополнительные параметры -->
      <div v-show="currentPage===9" class="column">
        <div class="columns is-desktop is-multiline">

          <!-- Москитная сетка -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <label class="label">Цена москитной сетки за м<sup>2</sup></label>
                <div class="control">
                  <input v-model.number="settings.mosquitoNetPrice" class="input" type="number"
                         placeholder="Цена москитной сетки">
                </div>
              </div>
            </div>
          </div>

          <!-- Микропроветривание -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <label class="label">Цена микропроветривания за шт. на каждую створку</label>
                <div class="control">
                  <input v-model.number="settings.microVentilationPrice" class="input" type="number"
                         placeholder="Цена микропроветривания">
                </div>
              </div>
            </div>
          </div>

          <!-- Монтаж -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <label class="label">Цена монтажа за м<sup>2</sup></label>
                <div class="control">
                  <input v-model.number="settings.mountingPrice" class="input" type="number"
                         placeholder="Цена монтажа">
                </div>
              </div>
            </div>
          </div>

          <!-- Скидка -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <label class="label">Скидка %</label>
                <div class="control">
                  <input v-model.number="settings.discount" class="input" type="number"
                         placeholder="Скидка">
                </div>
              </div>
            </div>
          </div>

          <!-- Телефон компании -->
          <div class="column is-half-desktop">
            <div class="box">
              <div class="field">
                <label class="label">Телефон компании (показывается, если возникла ошибка отправки заказа)</label>
                <div class="control">
                  <input v-model.number="settings.companyPhone" class="input" type="text"
                         placeholder="Телефон компании">
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Почта -->
      <div v-show="currentPage==='emails'" class="column">
        <div class="columns is-desktop is-multiline">
          <div class="column">
            <div class="box">

              <!-- Почта -->
              <div class="field">
                <label class="label">Почта для заявок. Можно указать несколько адресов через запятую</label>
                <div class="control">
                  <input v-model="settings.emails" class="input" type="text"
                         placeholder="Почта для заявок">
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- Пароль -->
      <div v-show="currentPage==='password'" class="column">
        <div class="columns is-desktop is-multiline">
          <div class="column">
            <div class="box">

              <!-- Новый пароль -->
              <div class="field">
                <label class="label">Новый пароль</label>
                <div class="control">
                  <input v-model="password" class="input" type="password"
                         placeholder="Новый пароль">
                </div>
              </div>

              <!-- Новый пароль еще раз -->
              <div class="field">
                <label class="label">Новый пароль еще раз</label>
                <div class="control">
                  <input v-model="password2" class="input" type="password"
                         placeholder="Новый пароль еще раз">
                </div>
              </div>

              <!-- Кнопка Изменить пароль -->
              <div class="field">
                <div class="control">
                  <button @click="changePassword()"
                          class="button is-success is-fullwidth">
                    Изменить пароль
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</div>

<script src="./js/jquery.min.js"></script>
<script src="./js/vue-resource.js"></script>
<script src="./js/admin.js"></script>

</body>
</html>
<?php } else { ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Настройки калькулятора стоимости пластиковых окон и балконов</title>
  <link rel="stylesheet" href="./css/bulma.min.css">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body class="ivan-calc">

<!-- Заголовок -->
<section class="hero is-dark is-light">
  <div class="hero-body">
    <div class="container">
      <h1 class="subtitle has-text-centered">
        Настройки калькулятора стоимости пластиковых окон и балконов
      </h1>
    </div>
  </div>
</section>

<div class="section">
  <div class="container">
    <div class="columns is-mobile is-centered">
      <div class="column is-full-mobile is-half-fullhd">
        <form action="#" class="form">

          <!-- Поле "Пароль" -->
          <div class="field">
            <label class="label">Пароль</label>
            <div class="control">
              <input name="password" class="input" type="password" placeholder="Пароль">
            </div>
          </div>

          <!-- Кнопка "Войти" -->
          <div class="field">
            <div class="control">
              <button type="submit" class="button is-link is-fullwidth">Войти</button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>


<script src="./js/jquery.min.js"></script>
<script src="./js/login.js"></script>
</body>
</html>
<?php } ?>