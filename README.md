# Secure Cloud Storage App

This repository contains the source code for a cloud storage application with a front-end implemented in HTML/CSS/JS, a PHP back-end following the MVC pattern, MariaDB for the database, and Nginx acting as a reverse proxy and load balancer.

## Features

- **Front-end:** The user interface is built using HTML, CSS, and JavaScript, providing a seamless experience for interacting with the secure cloud storage service.

- **Back-end (PHP MVC):** The PHP back-end follows the Model-View-Controller (MVC) pattern, enhancing code organization and maintainability. Key components include routers, controllers, models, and views.

- **Database (MariaDB):** MariaDB is used as the database management system to store and retrieve data efficiently.

- **Docker Containers:**
    - The application components (Nginx, MariaDB, and PHP) are each running in separate Docker containers for ease of deployment and scalability.

<br />
<hr>
<br />

## Security Architecture: Encryption details

### Master Key

The MasterKey is obtained using the PBKDF2 key derivation algorithm, taking the user's password and a random salt as input, which is saved in the database. 
This key is not directly stored in the database but is calculated at every login through the PBKDF2 algorithm. In case the user chooses to access in 
remember-me mode, initializing a session lasting 14 days (avoiding the need to log in each time), the MasterKey will be encrypted by the SessionKey and 
saved in the database. In this scenario, saving the MasterKey in the database is mandatory, as the user does not enter the password when opening the app, 
making it impossible to obtain the MasterKey.

### Session Key

The SessionKey is obtained through the PBKDF2 algorithm, taking a random salt (saved in the database) and the session token as input. Its purpose is to 
encrypt/decrypt the MasterKey each time the app is opened when the user initially logs in using remember-me mode. If the user logs in without selecting 
remember-me, this key is not used.

### Recovery Key

The RecoveryKey is randomly generated during registration and is used to recover the account in case of a forgotten password. This key is also used to 
encrypt/decrypt the CipherKey and the 2FA secret. In the database, it is saved in two formats: hashed and encrypted. The hash is used to verify that 
the RecoveryKey is correct when the user wants to recover the account in case of a forgotten password.

### Cipher Key

The CipherKey is randomly generated during registration and is used to encrypt the user's sensitive information, such as all files, file metadata, 
and more. It is encrypted by the RecoveryKey and saved in the database.

### Secret 2FA

The 2FA secret is randomly generated during registration and is used to generate OTP codes through applications like Google Authenticator. It is encrypted 
by the RecoveryKey and saved in the database.

    
