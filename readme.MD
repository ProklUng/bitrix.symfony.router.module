# Модуль для Битрикса, обеспечивающий работу с роутером Symfony и нативными битриксовыми маршрутами

- Роуты Symfony, сконфигурированные через Yaml файл (путь указывается в админке Битрикса)
- Роуты Битрикса D7 (начиная с версии `21.400.0` главного модуля), сконфигурированные через Yaml файл (путь указывается в админке Битрикса)

## Зачем?

Основа модуля - наработки по теме, но упакованные в бандлы и (или) отдельные классы. Не на всех проектах удается
развернуть - по техническим или административным причинам, - всю привычную машинерию. 
Посему этот функционал выносится в отдельный модуль.

## Установка

composer.json основного проекта:

```json
  "extra": {
    "installer-paths": {
      "./bitrix/modules/{$name}/": ["type:bitrix-d7-module", "type:bitrix-module"],
      "./bitrix/components/{$name}/": ["type:bitrix-d7-component", "type:bitrix-component"],
      "./bitrix/templates/{$name}/": ["type:bitrix-d7-template", "type:bitrix-theme"]
    }
  }
```

И:

```json
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/proklung/bitrix.symfony.router.module"
        }
    ]
```

```bash
$ composer require proklung/bitrix-symfony-router-module
```

Установить модуль `proklung.symfony.router` в административном интерфейсе сайта `bitrix/admin/partner_modules.php`

Добавить в `init.php`:

```php
use Bitrix\Main\Loader;

Loader::includeModule('proklung.symfony.router');
```

## Конфигурирование

После активации модуля появится подменю `Настройка роутера Symfony` в разделе `Контент`.

- Вкладка `Роуты Symfony`

  1) `Активность` - если чекбокс не отмечен, то функционал не работает.  
  2) `Путь к Yaml файлу с конфигурацией роутов` - путь к Yaml файлу с обычной конфигурацией роутов Symfony.
     Если пусто, то появится сообщение. Функционал работать не будет.
  3) `Путь к папке с кэшом Yaml файлов` - если не заполнено, то кэширования не будет.        

- Вкладка `Нативные роуты Битрикс`

  Если версия главного модуля ниже, чем `21.400.0`, то вкладки `Нативные роуты Битрикс` не будет.  

  1) `Активность` - если чекбокс не отмечен, то функционал не работает.
  2) `Файл php с описанием роутов (в папке /local/routes)` - файл, который будет конфигом роутов.
  
      После установки модуля (и если задействован функционал), то в `/bitrix/settings.php` внести (или отредактировать)
       секцию `routing` (все согласно [документации](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=013764)):
       
       ```php
         return [
         // ....
         'routing' => ['value' => [
             'config' => [
                 'test.php'
             ]
        ]
         ]
         ];
       ```
       
      Если указанный файл не существует в папке `/local/routes`, то он буде создан. Изменять его не нужно, так как
      в этом случае при следующем обновлении опций в админке изменения перезатруться.
  
  3) `Путь к Yaml файлу с конфигурацией нативных роутов Битрикса` - Путь к Yaml файлу с обычной конфигурацией роутов Symfony (с небольшими
      нюансами).
      
      ```yaml
      first_bitrix_route:
        path: /foo/{param}/
        controller: 'Proklung\Symfony\Router\ExampleController::cacheAction'
        methods: GET|POST
        requirements:
          param: '\d+'
        defaults:
          param: 'Russia'
      
      # Старые статические страницы
      # / => /index.php
      public_page:
        path: /
        controller: ''
        defaults:
          _public: true # Ключевой признак
      
      ```
      
      Если пусто (или указанный файл не существует), то появится сообщение. Функционал работать не будет. 
      
  4) `Путь к папке с кэшом Yaml файлов нативных роутов Битрикс` - если не заполнено, то кэширования не будет.   

## Прочее

1) Экземпляр `Symfony\Component\Routing\Router` (роуты Symfony) можно получить снаружи так:

```php
$router = \Proklung\Symfony\Router\SymfonyRoutes::getInstance();
```