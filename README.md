# Super Simple Console Twitter

## Installation
Make sure you have Docker installed. From a command prompt in the project's root folder, issue:

```bash
> make docker-start
```

This will build the necessary Docker containers and use Docker compose to bring them up. Next, issue:

```bash
> make docker-shell
```

To get a shell prompt from inside the PHP Docker container. Install dependencies:

```bash
> composer install
```

## Usage
### Post new message
```bash
php t.php <username> -- <message>
```
This is also user to create new users. An example:
```bash
php t.php fred -- My name is fred and this is my first message.
```
Existing usernames can be gleaned from inspecting [data.json](data.json)

### View a given user's messages
```bash
php t.php <username>
```
The provided username must be for an existing user.

### Follow another user
```bash
php t.php <username> follows <username>
```
The provided username must be for existing users.

### Show all message subscriptions (own posts, and those of users you are following)
```bash
php t.php <username> wall
```
The provided username must be for an existing user. Bill is subscribed to mitch and duke, so is a good wall to look at.
