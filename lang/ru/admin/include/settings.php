<?php

$MESS['MEDIACA_CROSSPOSTING_SETTINGS_TITLE'] = 'Кросcпостинг: настройки';

$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_TITLE'] = 'ВКонтакте';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_CLIENT_ID'] = 'Идентификатор приложения';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_OWNER_ID'] = 'Идентификатор пользователя/сообщества';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_FROM_GROUP'] = 'Публиковать от имени сообщества';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_ACCESS_TOKEN'] = 'Токен доступ';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_REFRESH_TOKEN'] = 'Токен обмена';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_ID_TOKEN'] = 'JWT пользователя';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_DEVICE_ID'] = 'Идентификатор устройства';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_GET_TOKENS'] = 'Получить токены ВКонтакте';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_UPDATE_TOKENS'] = 'Обновить токены';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_MESSAGE_TEMPLATE'] = 'Шаблон сообщения';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_VK_INSTRUCTION'] = '
<p>Действия необходимо выполнять под аккаунтом администратора сообщества ВКонтакте.</p>
<h2>Создание приложения и получение его идентификатора</h2>
<p><a href="https://id.vk.com/about/business/go">Войдите в сервис авторизации VK ID</a>.</p>
<p>Перейдите в раздел «Мои приложения», нажмите кнопку «Добавить приложение» и заполните поля формы по шагам:</p>
<ol>
    <li>Заполните поле с названием приложения произвольным значением и выберите платформу "Web".</li>
    <li>Укажите базовый домен и заполните доверенный Redirect URL значением "#DOMAIN#/bitrix/admin/mediaca-crossposting-settings.php".</li>
    <li>Шаг с указанием способов быстрого входа в web-приложение нужно пропустить, 
    настройки шага не используются в рамках модуля.</li>
</ol>
<p>После создания приложения настройте требуемые доступы, для этого перейдите во вкладку «Доступы» и активируйте расширенные доступы:</p>
<ul>
<li>Сообщества.</li>
<li>Фотографии.</li>
</ul>
<p>После настройки доступов вернитесь во вкладку «Приложение» и скопируйте значение поля «ID приложения».</p>
<p>Если остались вопросы, воспользуйтесь
<a href="https://id.vk.com/about/business/go/docs/ru/vkid/latest/vk-id/connection/create-application">официальной документацией</a>.</p>
<h2>Получение идентификатора сообщества</h2>
<p>Перейдите на страницу сообщества ВКонтакте, выберите пункт меню «Управление», 
скопируйте значение из строки «Номер сообщества — club229109218» и отредактируйте значение убрав слово «club»,
и добавив знак -, должно получиться значение вида "-229109218".</p>
';

$MESS['MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_TITLE'] = 'Телеграм';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_ACCESS_TOKEN'] = 'Токен доступа бота ';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_CHAT_USER_NAME'] = 'Юзернейм чата (с начальным @)';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_MESSAGE_TEMPLATE'] = 'Шаблон сообщения';
$MESS['MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_INSTRUCTION'] = '
<h2>Создание бота и получение токена доступа</h2>
<p>Для создания бота необходимо обратиться в Телеграм к боту <a href="https://t.me/botfather">@BotFather</a>, 
выполить нкоманду "/newbot" и заполнить имя бота, и юзернейм, ответное сообщение будет содержать токен доступа бота.</p>
<p>Созданного бота необходимо назначить администратором Телеграм-чата в который планируется выполнять кросспостинг.</p>
<p>Если остались вопросы, воспользуйтесь
<a href="https://core.telegram.org/bots/features#creating-a-new-bot">официальной документацией</a>.</p>';
