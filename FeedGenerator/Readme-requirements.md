## This Readme simply shows are the project requirements

Obbiettivo: sviluppo di un modulo per la generazione di feed prodotti


### DETTAGLI MODULO DA REALIZZARE:

Realizzare un modulo Magento2 per la generazione di feed (es. Facebook o Google Feed). Lo scopo del test è valutare 
la struttura e la qualità del codice, l'uso di design pattern, l'aderenza alle linee guida Magento e la sensibilità 
alle tematiche di performance.

L'idea è che lo sviluppo non occupi più di 8 ore, quindi non è fondamentale che il codice sia completo in tutte le sue 
parti, l'importante è che vengano indicate le aree escluse o che andrebbero rifinite maggiormente.


- Il modulo deve esporre un comando CLI che consenta di fare un export prodotti in csv secondo uno schema predefinito da
un vendor (es. GoogleShopping, Facebook).

- Il comando CLI deve processare tutti gli store e generare per ognuno un feed differente che verra' identificato dal 
nome: [store_code]_feed_[vendor].csv

- Configurare il comando per poter passare un input che specifichi per quali vendor e quali store processare

- Generare un file di log dettagliato per ogni creazione di feed, riempiendolo con I dati che sembrano utili (da ricordare
di non inserire dati potenzialmente sensibili)

- Avere un'attenzione particolare per le performance



Esempio di tracciato:

es. colonne Vendor1

```
sku, nome prodotto, descrizione, manufacturer, brand, link prodotto, immagine principale, prezzo di vendita
```
- l'attributo "brand" è da aggiungere come nuovo attributo eav con frontend input di tipo select e valori personalizzabili 
da backoffice


es. colonne Vendor2

come Vendor1 ma

- aggiungere:

```descrizione breve, totale disponibilità in stock```

- togliere:
```descrizione e manufacturer```

