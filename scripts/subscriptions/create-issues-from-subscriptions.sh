#!/bin/bash

mysql -h ${MYSQL_DM_HOST} -uroot -p${MYSQL_PASSWORD} dm -se \
  "select min(Date_debut), least(current_date, max(Date_fin)), group_concat(CONCAT('\'', Pays, '/', Magazine, '\'')) from abonnements" -N | \
  grep -Po '^[^\r]+' | while read -r min_issue_date max_issue_date publication_codes; do

    mysql -h ${MYSQL_COA_HOST} -uroot -p${MYSQL_PASSWORD} coa -se \
      "SELECT regexp_substr(publicationcode, '^[^/]+'), regexp_substr(publicationcode, '(?<=/).+'), issuenumber, filledoldestdate
       FROM inducks_issue WHERE publicationcode IN ($publication_codes) AND filledoldestdate BETWEEN '$min_issue_date' AND '$max_issue_date'" -N | \
      grep -Po '^[^\r]+' | while read -r country magazine issuenumber release_date; do
        echo "Released issue : $country/$magazine $issuenumber on $release_date"
        mysql -h ${MYSQL_DM_HOST} -uroot -p${MYSQL_PASSWORD} dm -se "
        INSERT IGNORE INTO abonnements_sorties (Pays, Magazine, Numero, Date_sortie, Numeros_ajoutes)
        VALUES ('$country', '$magazine', '$issuenumber', '$release_date', 0)"
      done

    mysql -h ${MYSQL_DM_HOST} -uroot -p${MYSQL_PASSWORD} dm -se \
      "INSERT IGNORE INTO numeros(Pays, Magazine, Numero, Etat, ID_Acquisition, AV, Abonnement, ID_Utilisateur)
         SELECT a.Pays, a.Magazine, sorties.Numero, 'bon', -1, 0, 1, a.ID_Utilisateur
         FROM abonnements a
         INNER JOIN abonnements_sorties sorties on sorties.Pays = a.Pays and sorties.Magazine = a.Magazine
         WHERE Numeros_ajoutes = 0;
       UPDATE abonnements_sorties SET Numeros_ajoutes=1;"
  done