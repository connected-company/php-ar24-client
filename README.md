# php-ar24-client
*Auteur : Léo Boiron (lboiron@com-company.fr)*

Librairie PHP pour le service LRE d'AR24 (https://www.ar24.fr/)

**Fonctionnalités** :
- Création d'un client sur l'environnement de test ou de production.
- Authentification avec un email et un jeton d'accès (token).
- Configuration du webhook et du timeout.
- Support de l'utilisation de l'API avec différents utilisateurs.
- Ajout de pièces-jointes.
- Envoi d'une LRE simple (Simple Registered Electronic Mail) ou d'une LRE eIDAS à un destinataire.

**Avertissements** :
- L'ajout des pièces-jointes n'est pas optimisé si le fichier envoyé est identique à tous les destinataires.
- Toutes les données de retour ne sont pas implémentées.
- Avant d'utiliser cette librairie, un utilisateur doit être **manuellement** configuré avec l'API.
- Dans le cas de l'utilisation des LRE eIDAS, un code OTP doit être **manuellement** récupéré.
- Seulement l'authentification par OTP est actuellement gérée pour les LRE eIDAS. 

**Sommaire** :
- [Création manuel de l'utilisateur](#cr%C3%A9ation-manuel-de-lutilisateur)
- [Configuration et création du client](#configuration-et-création-du-client)
- [Création d'un destinataire](#création-dun-destinataire)
- [Création d'une LRE](#création-dune-lre)
- [Ajouter une pièce-jointe](#ajouter-une-pièce-jointe)
- [Envoyer une LRE](#envoyer-une-lre)
- [Informations sur une LRE envoyée](#informations-sur-une-lre-envoyée)
- [Exemple](#exemple)
- [Exceptions](#exceptions)

______________
# Création manuel de l'utilisateur

### 1 - Créer un utilisateur
> *Chaque expéditeur d’une lettre recommandée doit posséder un compte AR24.*

https://www.ar24.fr/documentation/api/#api-UserGroup-create_user-1.0.0

### 2 - Ajouter les accès API à l'utilisateur si besoin
> *L’utilisateur AR24 recevra un mail de notre part pour accepter la demande de rattachement à votre API.*

https://www.ar24.fr/documentation/api/#api-UserGroup-access_request-1.0.0

### 3 - Récupération du code OTP pour les LRE eIDAS
> *Afin d’effectuer un envoi de LRE eIDAS il est nécessaire d’identifier l’expéditeur du courrier.
Cette identification passe par des OTP pour l’expéditeur lorsqu’il souhaite faire un envoi via l’API.*

https://www.ar24.fr/documentation/api/#api-eidas-send_eidas_mail

______________
# Configuration et création du client
### 1 - Configuration
La création du client nécessite un objet `Configuration` qui permet de spécifier l'environnement, le webhook et le timeout.
```php
use Connected\Ar24\Component\Configuration;

public function __construct(string $environment, string $webhook, float $timeout = self::TIMEOUT);
```
L'argument `$environment` doit être `demo` ou `prod`.
Le timeout par défaut est de 20 secondes pour permettre l'envoi de pièces-jointes imposantes.

### 2 - Création du client
Le client doit être instancié avec l'objet `Configuration` précédemment créé.
```php
use Connected\Ar24\Client;

public function __construct(Configuration $configuration);
```

### 3 - Création d'un utilisateur
L'authentification et l'expédition d'une LRE nécessite la création d'un objet `User`. Le code OTP peut être fourni.
```php
use Connected\Ar24\Model\User;

public function __construct(string $email, string $token, string $otpCode = null);
```
Plusieurs utilisateurs peuvent être attachés au client pour changer le destinataire pendant un traitement.

### 4 - Ajout de l'utilisateur
L'utilisateur doit être ajouté au client, qui se chargera de vérifier ses identifiants auprès de l'API.
```php
Client::addUser(User $user): self
```

### Résumé
```php
$configuration = new Configuration('demo', 'https://webook-url.tld/api');
$client = new Client($configuration);

$user = new User('expediteur@domain.tld', '132465798132456798132465789', 'OTPCODE123456789);
$client->addUser($user);
```

______________
# Création d'un destinataire
La classe `Recipient` permet de créer le destinataire d'une LRE. Les informations de nom, prénom, email, raison sociale et de référence client peuvent être ajoutés.
```php
use Connected\Ar24\Model\Recipient;

public function __construct(
  string $firstname,
  string $lastname,
  string $email,
  ?string $company = null,
  ?string $reference = null
) 
```

______________
# Création d'une LRE
Pour paramétrer une LRE, le destinataire doit être fourni avec le contenu de celle-ci. Une référence de dossier et de facture peuvent être ajoutées.
```php
use Connected\Ar24\Model\Email;

public function __construct(
  Recipient $recipient, 
  string $content, 
  ?string $referenceDossier = null, 
  ?string $referenceFacturation = null
);
```

______________
# Ajouter une pièce-jointe
L'ajout d'une pièce-jointe nécessite de fournir le chemin du fichier à ajouter.
```php
use Connected\Ar24\Model\Attachment;

public function __construct(string $filepath);
```
L'objet `Attachment` doit ensuite être ajouté à la LRE.
```php
Email::addAttachment(Attachment $attachment);
```

______________
# Envoyer une LRE
Une fois le destinataire ajouté ainsi que les éventuelles pièces-jointes, la LRE peut être envoyée depuis le client en spécifient le compte expéditeur.
Dans le cadre d'une LRE simple :
```php
Client::sendSimpleRegisteredEmail(User $user, Email $email);
```
Ou pour une LRE eIDAS (*le code OTP doit être fourni lors de la création du `User`*) :
```php
Client::sendEidasEmail(User $user, Email $email);
```

______________
# Informations sur une LRE envoyée
Des informations peuvent être récupérées une fois la LRE envoyée.
```php
Client::getEmailInformations(User $user, int $id);
```

______________
# Exemple
```php
// Configuration et création du client.
$configuration = new Configuration('demo', 'https://webook-url.tld/api');
$client = new Client($configuration);

// Création et ajout d'un utilisateur.
$user = new User('leo.boiron@domain.tld', '123456789123456789123456789', 'OTPCODE123456789');
$client->addUser($user);

// Création du destinataire et de la LRE.
$recipient = new Recipient('Léo', 'Boiron', 'lboiron@domain.tld');
$email = new Email($recipient, 'Contenu LRE');

// Ajout d'une pièce-jointe.
$attachment = new Attachment('/var/www/html/documents/file.pdf');
$email->addAttachment($attachment);

// Envoi de la LRE simple.
$response = $client->sendSimpleRegisteredEmail($user, $email);

// Envoi de la LRE eIDAS.
$response = $client->sendEidasEmail($user, $email);

// Récupération d'informations sur la dernière LRE envoyée.
$client->getEmailInformations($user, $response->getId());
```

______________
# Exceptions
La librairie fournit deux types d'exceptions :
- `Ar24ClientException` : soulevé lorsque un paramètre est mal renseigné au sein de la librairie (timeout inférieur à 0, mauvais email, ...).
- `Ar24ApiException` : soulevé lorsque l'API d'AR24 remonte une erreur ou est en maintenance.
