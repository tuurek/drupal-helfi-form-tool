*** Settings ***
Metadata        Lomake reg test set version 0.1
Metadata        Executed At                 ${selected-env}
Library         SeleniumLibrary
Library         OperatingSystem
Library         String
Resource        ../resources/variables/lomake-Variables.robot
Resource        ../resources/variables/common-Variables.robot
Resource        ../resources/keywords/lomake-Keywords.robot
Resource        ../resources/keywords/common-Keywords.robot
Test Timeout    900 seconds
Test Teardown   Run keywords    Capture Page Screenshot     Delete All Cookies  Close All Browsers
Force Tags      smoke

*** Test Cases ***
#########################################################################################
# Mitä testataan?
# 
# 1. 
# 2. 
#
# Alkuvaatimukset
# - 
#########################################################################################
# robot -d logit --variable environment:dev-chrome --exitonfailure tests/lomake-Check-tjpt-page-functionality.robot

Väliaikainen testi jossa avataan lomake sivu
# 
    [Tags]  critical
    Select test data and open browser
    Go To                                                   ${dev_lomake-todistusjaljennospyynto-tilaus-direct_url}
    Log in using suomi.fi authentication - FI               ${testuser1-lomake-hetu}
    Accept all cookies
    Capture Page Screenshot
    # Tarkistetaan, että kaikki todistus valinnat ovat valittavissa
    Valitse tilattavaksi todistukseksi                      Peruskoulun päättötodistus
    Capture Page Screenshot
    Valitse tilattavaksi todistukseksi                      Peruskoulun erotodistus
    Capture Page Screenshot
    Valitse tilattavaksi todistukseksi                      Lisäopetuksen 10.lk todistus
    Capture Page Screenshot
    Valitse tilattavaksi todistukseksi                      Lukion päättötodistus
    Capture Page Screenshot
    Valitse tilattavaksi todistukseksi                      Lukion erotodistus
    Capture Page Screenshot
    # Lisää koulun nimi
    Input Text                                              ${lomake-koulun-nimi-input}                             Koulun nimi tähän
    Capture Page Screenshot
    # Valitse toimitustavaksi nouto
    Click Element                                           ${lomake-tjpt-toimitustapa-nouto-radiobutton-FI}
    Wait Until Page Contains                                Noudetaan kasvatuksen ja koulutuksen toimialan arkistolta
    Capture Page Screenshot
    # Valitse toimitustavaksi postiennakko
    Click Element                                           ${lomake-tjpt-toimitustapa-postiennakko-radiobutton-FI}
    Genarate test data for postiennakko toimitustapa
    Fill postiennakko information
    # Rekisteriseloste
    Click Element                                           ${lomake-tjpt-rekisteriseloste-checkbox}
    Capture Page Screenshot
    Sleep       10
    [Teardown]    NONE