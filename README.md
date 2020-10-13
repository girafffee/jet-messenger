
# JetMessenger


_WP Plugin for sending notifications to your Messenger_

## Bot for Telegram

- Создаем канал, куда добавляем бота с правами администратора
- Добавляем **токен**
- Добавляем **слаг канала**
-- ***Результат***: можем добавлять уведомления на странице General Notifications, по которым будут приходить сообщения в канал.

- Добавляем слаг своего юзера в телеграмме **без '@'**
- Нажимаем **Sync** и в контенте попапа копируем **код**, который нужно отправить **боту в лс**
- Отправляем код боту в лс, тем самым подтверждая подключение своего телеграмма
-- ***Результат***: можем добавлять уведомления, которые будут приходить в ваш личный чат с ботом.

# Customize Notifications

На данный момент активны 2 вида хука, на которые срабатывают уведомления и условия, которые можно к ним применить:

 1. ***New Post***
	 - ***Author*** в Action Value нужно вписать **ID** автора поста
	 - ***Taxonomy*** в Action Value нужно вписать **SLUG** таксономии
	 - ***Post Type*** в Action Value нужно вписать **SLUG** пост-типа
 2. ***New Comment***
	 - ***Author*** в Action Value нужно вписать **ID** автора поста, к которому был добавлен комментарий
	 - ***Taxonomy*** в Action Value нужно вписать **SLUG** таксономии поста, к которому был добавлен комментарий
	 - ***Post Type*** в Action Value нужно вписать **SLUG** пост-типа поста, к которому был добавлен комментарий
	 
*others will come later...*

## Macroses in Message
*Макросы нужно вписывать таким образом: %macros_name%*

## New Post
- `ID` - ID добавленного поста
- `post_author` - ФИО и логин автора
- `post_date` - Дата добавления поста
- `post_title` - Заголовок поста
- `post_excerpt` - Краткое описание поста
- `post_parent` - Родитель поста (*пока что выходит только ID*)
- `post_modified` - Дата редактирования поста
- `guid` - Ссылка на пост

## New Comment
- `comment_post_ID` - ID поста, к которому был добавлен комментарий
- `comment_author` - Логин автора
- `comment_author_email` - Почта автора
- `comment_author_url` - Ссылка автора
- `comment_content` - Текст комментария
- `user_id` - ФИО и логин автора
- `comment_author_IP` - IP-адрес автора
- `comment_agent` -  [User-Agent](https://developer.mozilla.org/uk/docs/Web/HTTP/%D0%97%D0%B0%D0%B3%D0%BE%D0%BB%D0%BE%D0%B2%D0%BA%D0%B8/User-Agent)
- `comment_date` - Дата публикации комментария
