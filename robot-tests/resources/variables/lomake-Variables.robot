*** Variables ***

${environment}      null

${testuser1-lomake-hetu}                                        210281-9988     #150552-9979     # Taavetti Järvites
                                                                                # Tarvitsee profiilin
# Urlit
# DEV
${dev_lomake-login_url}                                         https://lomaketyokalu.dev.hel.ninja/fi
${dev_lomake-direct-logout_url}                                 https://lomaketyokalu.dev.hel.ninja/user/logout
${dev_lomake-todistusjaljennospyynto-tilaus-direct_url}         https://lomaketyokalu.dev.hel.ninja/fi/form/todistusjaljennospyynto-tilaus
${dev_example-app_url}                                          https://example-ui.dev.hel.ninja/
                                      
# Valitse tilattava todistus
## tjpt = Todistusjäljennöspyyntö tilaus
## vtt = Valitse tilattava todistus
${lomake-tjpt-vtt-peruskoulun-päättö-radiobutton-FI}            //label[contains(.,'Peruskoulun päättötodistus')]
${lomake-tjpt-vtt-peruskoulun-erotodistus-radiobutton-FI}       //label[contains(.,'Peruskoulun erotodistus')]
${lomake-tjpt-vtt-lisaopetuksen-10lk-todistus-radiobutton-FI}   //label[contains(.,'Lisäopetuksen 10.lk todistus')]
${lomake-tjpt-vtt-lukion-paattotodistus-radiobutton-FI}         //label[contains(.,'Lukion päättötodistus')]
${lomake-tjpt-vtt-lukion-erotodistus-radiobutton-FI}            //label[contains(.,'Lukion erotodistus')]
# Todistuksen antanut Helsinkiläinen koulu
${lomake-koulun-nimi-input}                                     //input[@data-drupal-selector='edit-todistuksen-antanut-helsinkilainen-koulu']

# Toimitustapa
${lomake-tjpt-toimitustapa-postiennakko-radiobutton-FI}         //label[contains(.,'Postiennakko')]
${lomake-tjpt-toimitustapa-nouto-radiobutton-FI}                //label[contains(.,'Nouto')]

${lomake-tjpt-toimitustapa-etunimi-field-FI}                    id=edit-valitse-toimitustapa-cod-first-name
${lomake-tjpt-toimitustapa-sukunimi-field-FI}                   id=edit-valitse-toimitustapa-cod-last-name
${lomake-tjpt-toimitustapa-osoite-field-FI}                     id=edit-valitse-toimitustapa-cod-street-address
${lomake-tjpt-toimitustapa-postinumero-field-FI}                id=edit-valitse-toimitustapa-cod-zip-code
${lomake-tjpt-toimitustapa-kaupunki-field-FI}                   id=edit-valitse-toimitustapa-cod-city
${lomake-tjpt-toimitustapa-puhelinnumero-field-FI}              id=edit-valitse-toimitustapa-cod-phone-number

# Rekisteri seloste
${lomake-tjpt-rekisteriseloste-checkbox}                        //input[@data-drupal-selector='edit-privacy-policy-acceptance']









