# FirstPoster
Мониторинг новых записей на стенах пользователей\сообществ и добавление первых комментариев к ним.
### Установка
> Предполагается использование https://c9.io


```
git clone https://github.com/Unkodero/firstposter.git
composer install
```
### Доступные комманды
+ php firstposter wall:add -w="WALL_ID" \<MESSAGE\> - добавить стену.
+ php firstposter wall:remove -w="WALL_ID" - удалить стену.
+ php firstposter wall:list - список стен в базе.
+ php firstposter poste \<ACCESS_TOKEN\> - запустить мониторинг.
+ + Для комманды php firstposter poste доступна опция --delay (-d), которая задает задержку между повторениями в секундах. По умолчанию - 3 секунды.

> Если при работе со стеной происходит ошибка - она автоматически удаляется из очереди. 

## Как получить Access Token
+ Переходим по ссылке https://oauth.vk.com/authorize?client_id=3682744&v=5.7&scope=wall,offline&redirect_uri=http://oauth.vk.com/blank.html&display=page&response_type=token
+ Предоставляем доступ (официальное приложение iPad)
+ В адресной строке браузера копируем токен