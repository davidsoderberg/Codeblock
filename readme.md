codeblock
=========

Repository for codeblock.se

## Saker som jag är medveten om som finns i koden
* I vissa kontrollrar och modeller finns det kommentarer som autoskapas då jag använder kommandon för att skapa dem
* Rättighet och roller är inte implementerat ännu då jag inte kom på någon bra lösning att kunna sätta ut vad som krävde vilken rättighet.

## Upplägg på koden

model.php är basmodellen som alla andra modeller äver av och säger till att allt som sparas via dessa modeller valideras enligt regler som specifiseras i varje model och de modellerna som har relationer har en metod deleting som tar bort alla relaterade rader när ägande rad tas bort.

all hantering av databasen sköts i så kallade repon som alla implementerar ett interface för varje modell som finns. Alltså finns det nästan en klass per modell och ett interface per modell som varje repo implementerar och alla klasser ärver från en basklass som många klasser använder föräldermetoder ifrån.
