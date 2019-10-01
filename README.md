# php-ar24-client
- MAINTAINER : Léo Boiron

Librairie PHP pour le service LRE d'AR24

**Fonctionnalités** :
- Création d'un client sur l'environnement de test ou de production.
- Authentification avec un email et un jeton d'accès (token).
- Configuration du webhook et du timeout.
- Création d'un expéditeur avec le compte utilisé par l'authentification.
- Ajout de pièces-jointes.
- Envoi d'une LRE simple (Simple Registered Electronic Mail) à un destinataire.

**Avertissements** :
- La librairie ne prend pas en charge plusieurs comptes sur une même instance de `Client`.
- L'ajout des pièces-jointes n'est pas optimisé si le fichier envoyé est identique à tous les clients.
- Le compte authentifié est l'expéditeur.
- Toutes les données de retour ne sont pas implémentées.
- Avant d'utiliser cette librairie, un utilisateur doit être **manuellement** configuré avec l'API.

______________
# Création manuel de l'utilisateur

### 1 - Créer un utilisateur
> *Chaque expéditeur d’une lettre recommandée doit posséder un compte AR24.*

https://www.ar24.fr/documentation/api/#api-UserGroup-create_user-1.0.0

### 2 - Ajouter les accès API à l'utilisateur si besoin
> *L’utilisateur AR24 recevra un mail de notre part pour accepter la demande de rattachement à votre API.*

https://www.ar24.fr/documentation/api/#api-UserGroup-access_request-1.0.0

______________
# Configuration et création du client
### 1 - Authentification et création de l'expéditeur
L'authentification et l'expédition d'une LRE nécessite la création d'un objet `Sender`
```
use Connected\Ar24\Model\Sender;

public function __construct(string $email, string $token);
```
### 2 - Configuration de la librairie
La création du client nécessite un objet `Configuration` qui permet de spécifier l'environnement, le webhook et le timeout.
```
use Connected\Ar24\Component\Configuration;

public function __construct(Sender $sender, string $environment, string $webhook, float $timeout = self::TIMEOUT);
```
L'argument `$environment` doit être `demo` ou `prod`.
Le timeout par défaut est de 20 secondes pour permettre l'envoi de pièces-jointes imposantes.

### 3 - Création du client
Le client doit être instancié avec l'objet `Configuration`.
```
use Connected\Ar24\Client;

public function __construct(Configuration $configuration);
```

### Résumé
```
$sender = new Sender('expediteur@domain.tld', '132465798132456798132465789');
$configuration = new Configuration($sender, 'demo', 'https://webook-url.tld/api');
$client = new Client($configuration);
```

______________
# Création d'un destinataire
La classe `Recipient` permet de créer le destinataire d'une LRE. Les informations de nom, prénom, email, raison sociale et de référence client peuvent être ajoutés.
```
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
# Création d'une LRE (Simple Registered Electronic Mail)
Pour paramétrer une LRE, le destinataire doit être fourni avec le contenu de celle-ci. Une référence de dossier et de facture peuvent être ajoutées.
```
use Connected\Ar24\Model\SimpleRegisteredEmail;

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
```
use Connected\Ar24\Model\Attachment;

public function __construct(string $filepath);
```
L'objet `Attachment` doit ensuite être ajouté à la LRE.
```
SimpleRegisteredEmail::addAttachment(Attachment $attachment);
```

______________
# Envoyer la LRE

Une fois le destinataire ajouté ainsi que les éventuelles pièces-jointes, la LRE peut être envoyée depuis le client.
```
Client::sendSimpleRegisteredEmail(SimpleRegisteredEmail $simpleRegisteredEmail);
```

______________
# Informations sur une LRE envoyée
Des informations peuvent être récupérées une fois la LRE envoyée.
```
Client::getSimpleRegisteredEmailInformations(int $id);
```

______________
# Exemple
```
// Configuration et création du client.
$sender = new Sender('leo.boiron@domain.tld', '123456789123456789123456789');
$configuration = new Configuration($sender, 'demo', 'https://webook-url.tld/api');
$client = new Client($configuration);

// Création du destinataire et de la LRE.
$recipient = new Recipient('Léo', 'Boiron', 'lboiron@domain.tld');
$simpleRegisteredEmail = new SimpleRegisteredEmail($recipient, 'Contenu LRE');

// Ajout d'une pièce-jointe.
$attachment = new Attachment('/var/www/html/documents/file.pdf');
$simpleRegisteredEmail->addAttachment($attachment);

// Envoi de la LRE.
$response = $client->sendSimpleRegisteredEmail($simpleRegisteredEmail);

// Récupération d'informations sur la LRE.
$client->getSimpleRegisteredEmailInformations($response->getId());
```

______________
# Exceptions
La librairie fournit deux types d'exceptions :
- `Ar24ClientException` : soulevé lorsque un paramètre est mal renseigné au sein de la librairie (timeout inférieur à 0, mauvais email, ...).
- `Ar24ApiException` : soulevé lorsque l'API d'AR24 remonte une erreur ou est en maintenance.
