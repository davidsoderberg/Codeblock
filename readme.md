Codeblock [![Build Status](https://snap-ci.com/davidsoderberg/codeblock/branch/master/build_image)](https://snap-ci.com/davidsoderberg/codeblock/branch/master)
=========
Repository for codeblock.se

## Installation instructions
1. Install [composer](https://getcomposer.org/) and run `composer install` in this directory.
2. Create database and database user with all permissions.
3. create an .env from .env.example and add all your config values in there.
4. Run `php artisan Install`.
5. Run `php artisan websocket` with [supervisor](http://supervisord.org/).

## Saker som jag är medveten om som finns i koden
* I vissa kontrollrar och modeller finns det kommentarer som autoskapas då jag använder kommandon för att skapa dem

## Upplägg på koden
Model.php är basmodellen som alla andra modeller äver av och säger till att allt som sparas via dessa modeller valideras enligt regler som specifiseras i varje model och de modellerna som har relationer har en metod deleting som tar bort alla relaterade rader när ägande rad tas bort.
All hantering av databasen sköts i så kallade repon som alla implementerar ett interface för varje modell som finns. Alltså finns det nästan en klass per modell och ett interface per modell som varje repo implementerar och alla klasser ärver från en basklass som många klasser använder föräldermetoder ifrån.
