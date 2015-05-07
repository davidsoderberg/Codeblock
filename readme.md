Codeblock
=========
Repository for codeblock.se

## Installation instructions
1. Create database and database user with all permissions.
2. create an .env from .env.example and add all your config values in there.
3. Run `php artisan Install`.
4. Create user on the website.
5. Change the default role for a standard user on `/roles`. 

## Saker som jag är medveten om som finns i koden
* I vissa kontrollrar och modeller finns det kommentarer som autoskapas då jag använder kommandon för att skapa dem

## Upplägg på koden
Model.php är basmodellen som alla andra modeller äver av och säger till att allt som sparas via dessa modeller valideras enligt regler som specifiseras i varje model och de modellerna som har relationer har en metod deleting som tar bort alla relaterade rader när ägande rad tas bort.
All hantering av databasen sköts i så kallade repon som alla implementerar ett interface för varje modell som finns. Alltså finns det nästan en klass per modell och ett interface per modell som varje repo implementerar och alla klasser ärver från en basklass som många klasser använder föräldermetoder ifrån.
