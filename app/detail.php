<!DOCTYPE html>
<html>
  <head>
    <title>VerA.web - modify a user (#4711)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="backle.css" type="text/css"/>
    <link href="lib/bootstrap.css" rel="stylesheet" media="screen">

    <script src="lib/jquery.js"></script>
    <script src="lib/ui/jquery-ui.js"></script>
    <script src="lib/angular.min.js"></script>
    <script src="lib/angular-resource.min.js"></script>
    <script src="./detail.js"></script>
    
  </head>
  <body>

     <br>
     <div class="container">
       <div class="panel panel-success">
         <div class="panel-heading">
           <h3 id="headline">modify a user (#4711)</h3>
           <p>As Produktverantwortlicher möchte ich beliebige Stories in einem Backlog anlegen können, 
             damit ich mir merken kann, was alles zu tun ist.</p>
          </div>
           <div class="panel-body">
            <div class="row">              
              <div class="col-md-8">
                <h4>Requirements:</h4>
                <ul>
                  <li>Es ist möglich bestehende Felder zu ändern (Neuer Wert oder auf Null-Wert zurück setzen)</li>
                <li>Es ist möglich das Passwort neu zu setzen</li>
                  <li>Es ist möglich Felder zu ändern ohne das Passwort neu setzen zu müssen</li>
                  <li>Es ist möglich Listenattributen Einträge hinzuzufügen</li>
                  <li>Es ist möglich Einträge von Listenattributen zu entfernen</li>
                  <li>Es ist möglich Einträge in Listenattribute zu ändern</li>
                </ul>
                <h4>Abgrenzungskriterien</h4>
                <ul>
                <li>Das Ändern von Listenattributen kann durch ein neusetzen der Liste erfolgen</li>
                <li>Die Änderungsoperation dürfen auf mehrere Methoden aufgeteilt werden, wenn dies sinnvoll erscheint</li>
                <li>Es ist tolerierbar das auch nicht geänderte Daten überschrieben werden</li>
                </ul>
                <h4>Constraints</h4>
                <ul>
                  <li>Der UserName muss immer mit übergeben werden (OSIAM-Bug)</li>
                  <li>Der Connector macht keine Überprüfung von gesetzten Feldern sondern dies wird vom Server übernommen</li>
              </ul>
                <h4>Acceptance Criteria:</h4>
                <ul>
                  <li>Es soll keine generische Schnittstelle sein</li>
                </ul>
                <h4>User Acceptance Tests</h4>
                <ul>
                  <li>Es soll der NickName geändert werden wobei alle anderen Felder unverändert bleiben</li>
                  <li>Es soll ein Test existieren der alle Felder ändert</li>
                  <li>Es wird eine Email-Adresse hinzugefügt, entfernt, geändert (hinzugefügt und entfernt)</li>
                  <li>Das ändern von Werten ändert nicht das Passwort</li>
                  <li>Das ändern des Passwortes ist möglich</li>
                <li>Die fehlerbehandung wird demonstriert</li>
                </ul>
              </div>
              <div class="col-md-4">
                <br/>
                <div class="row">
                  <div class="col-xs-4"><strong>Sprint</strong></div> <div class="col-xs-3">3</div>
                </div>
                <div class="row">
                <div class="col-xs-4"><strong>StoryPoints</strong></div> <div class="col-xs-3">13</div>                
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>Author</strong></div> <div class="col-xs-3">smancke</div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-4"><strong>Erstellt</strong></div> <div class="col-xs-8">08.07.2013 13:20 Uhr</div>                
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>Aktualisiert</strong></div> <div class="col-xs-8">08.07.2013 14:00 Uhr</div>
                </div>
                <div class="row">
                  <div class="col-xs-4"><strong>Erledigt</strong></div> <div class="col-xs-8">10.11.2013 11:00 Uhr</div>
                </div>

                
                <hr/>
                <h5><strong>Attachments</strong></h5>
                <ul>
                  <li><a href="">Filename_1.jpg</a></li>
                  <li><a href="">Und_noch_ein_Dokument.jpg</a></li>
                  <li><a href="">Undnochetwas.jpg</a></li>
                </ul>
                <hr/>
                <h5><a href="">Robert Linden</a> hat einen Kommentar hinzugefügt - 09.08.2013 15:12 Uhr</h5>
                <p>Sollte in zwei stories (PUT/PATCH) aufgeteilt werden.
                  enthaelt noch nicht die gruppenzuordnung</p>
                <hr/>
                <h5><a href="">Sebastian Mancke</a> hat einen Kommentar hinzugefügt - 09.08.2013 15:12 Uhr</h5>
                <p>Sollte in zwei stories (PUT/PATCH) aufgeteilt werden.
                  enthaelt noch nicht die gruppenzuordnung</p>
                <hr/>
                <h5><a href="">Sebastian Mancke</a> hat einen Kommentar hinzugefügt - 09.08.2013 15:12 Uhr</h5>
                <p>Sollte in zwei stories (PUT/PATCH) aufgeteilt werden.
                  enthaelt noch nicht die gruppenzuordnung</p>
              </div>
            </div>
          </div>
       </div>
     </div>
     
  </body>
</html>
