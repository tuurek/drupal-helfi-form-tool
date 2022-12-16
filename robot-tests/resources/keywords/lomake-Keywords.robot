*** Keywords ***

Select test data and open browser
    Select test data depending on the selected env  ${environment}
    Select url depending on the selected env        ${environment}
    Browser specific sleep                          ${environment}    
    Log                                             ${environment}

Browser specific sleep
    [Arguments]    ${selected-env}
    ## firefox azuressa vaatii jostain syystä sleepin ekan kerran selaimen avaamisen jälkeen
    ${TYPE}=    Set Variable    ${selected-env}
    Run Keyword If              '${TYPE}' == 'dev-firefox'              Sleep           30
    ...     ELSE IF             '${TYPE}' == 'test-firefox'             Sleep           30
    ...     ELSE IF             '${TYPE}' == 'stage-firefox'            Sleep           30
    ...     ELSE                                                        Sleep           1

Select url depending on the selected env
    [Arguments]    ${selected-env}
    # timeout browser opening if no browser available from selenium in 1 minute
    [Timeout]      1 minute

    ${TYPE}=    Set Variable    ${selected-env}
    Run Keyword If              '${TYPE}' == 'dev-firefox'              Open Browser    ${lomake_url}   firefox
    ...     ELSE IF             '${TYPE}' == 'dev-chrome'               Open Browser    ${lomake_url}   gc
    ...     ELSE IF             '${TYPE}' == 'dev-edge'                 Open Browser    ${lomake_url}   edge
    ...     ELSE IF             '${TYPE}' == 'test-firefox'             Open Browser    ${lomake_url}   firefox
    ...     ELSE IF             '${TYPE}' == 'test-chrome'              Open Browser    ${lomake_url}   gc
    ...     ELSE IF             '${TYPE}' == 'test-edge'                Open Browser    ${lomake_url}   edge
    ...     ELSE IF             '${TYPE}' == 'stage-firefox'            Open Browser    ${lomake_url}   firefox
    ...     ELSE IF             '${TYPE}' == 'stage-chrome'             Open Browser    ${lomake_url}   gc
    ...     ELSE IF             '${TYPE}' == 'stage-edge'               Open Browser    ${lomake_url}   edge
    ...     ELSE                '${TYPE}' == '7'                        temp
    Maximize Browser Window

Select test data depending on the selected env
    [Arguments]                 ${selected-env}

    ${TYPE}=    Set Variable    ${selected-env}
    Run Keyword If              '${TYPE}' == 'dev-firefox'              Select dev test data and urls
    ...     ELSE IF             '${TYPE}' == 'dev-chrome'               Select dev test data and urls
    ...     ELSE IF             '${TYPE}' == 'dev-edge'                 Select dev test data and urls
    ...     ELSE IF             '${TYPE}' == 'test-firefox'             Select test env test data and urls
    ...     ELSE IF             '${TYPE}' == 'test-chrome'              Select test env test data and urls
    ...     ELSE IF             '${TYPE}' == 'test-edge'                Select test env test data and urls
    ...     ELSE IF             '${TYPE}' == 'stage-firefox'            Select stage env test data and urls
    ...     ELSE IF             '${TYPE}' == 'stage-chrome'             Select stage env test data and urls
    ...     ELSE IF             '${TYPE}' == 'stage-edge'               Select stage env test data and urls
    ...     ELSE                '${TYPE}' == '7'                        temp

Select dev test data and urls
    # Env
    Set Suite Variable          ${lomake-selected-env}                  dev-
    
    # Url
    Set Suite Variable          ${lomake_url}                           ${dev_lomake-login_url}
    Set Suite Variable          ${example-app_url}                      ${dev_example-app_url}
 
Select test env test data and urls
    # Env
    Set Suite Variable          ${lomake-selected-env}                            test-

Select stage env test data and urls
    # Env
    Set Suite Variable          ${lomake-selected-env}                            stage-
    
Select prod test data and urls
    # Env
    Set Suite Variable          ${lomake-selected-env}                            prod-



Change language to FI
    Wait Until Page Contains Element    ${Your-profile-login-page-language-dropdown-button}                     20
    ${passed} =                         Run Keyword And Return Status           Page Should Contain             ${Your-profile-login-page-text1-EN}
    Run Keyword If                      ${passed}                               Change language to FI part 2

Change language to FI part 2
    Click Element                       ${Your-profile-login-page-language-dropdown-button}
    Click Element                       ${Your-profile-login-page-language-dropdown-FI}
    Page Should Not Contain             ${Your-profile-login-page-text1-EN}

Valitse tilattavaksi todistukseksi
    [Arguments]                         ${todistus}

    ${TYPE}=    Set Variable            ${todistus}
    Run Keyword If              '${TYPE}' == 'Peruskoulun päättötodistus'       Click Element    ${lomake-tjpt-vtt-peruskoulun-päättö-radiobutton-FI}
    ...     ELSE IF             '${TYPE}' == 'Peruskoulun erotodistus'          Click Element    ${lomake-tjpt-vtt-peruskoulun-erotodistus-radiobutton-FI}
    ...     ELSE IF             '${TYPE}' == 'Lisäopetuksen 10.lk todistus'     Click Element    ${lomake-tjpt-vtt-lisaopetuksen-10lk-todistus-radiobutton-FI}
    ...     ELSE IF             '${TYPE}' == 'Lukion päättötodistus'            Click Element    ${lomake-tjpt-vtt-lukion-paattotodistus-radiobutton-FI}
    ...     ELSE IF             '${TYPE}' == 'Lukion erotodistus'               Click Element    ${lomake-tjpt-vtt-lukion-erotodistus-radiobutton-FI}

Genarate test data for postiennakko toimitustapa
# Etunimi
    Set Suite Variable                  $etunimi   Late
# Sukunimi
    Set Suite Variable                  $sukunimi   Lomake
# Katuosoite
    ${random-kotiosoite-temp} =         Generate Random String  8  [LOWER]
    Set Suite Variable                  $random-kotiosoite   Osoite ${random-kotiosoite-temp} 1a
# Postinumero
    ${random-postinumero-temp} =        Generate Random String  5  [NUMBERS]
    Set Suite Variable                  $random-postinumero   ${random-postinumero-temp}
# Kaupunki
    Set Suite Variable                  $kaupunki   Lande
# Puhelinnumero
    ${random-puhnum-temp} =             Generate Random String  5  [NUMBERS]
    Set Suite Variable                  $random-puhnum   0666${random-puhnum-temp}

Fill postiennakko information
    Input Text                          ${lomake-tjpt-toimitustapa-etunimi-field-FI}            ${etunimi}
    Input Text                          ${lomake-tjpt-toimitustapa-sukunimi-field-FI}           ${sukunimi}
    Input Text                          ${lomake-tjpt-toimitustapa-osoite-field-FI}             ${random-kotiosoite}
    Input Text                          ${lomake-tjpt-toimitustapa-postinumero-field-FI}        ${random-postinumero}
    Input Text                          ${lomake-tjpt-toimitustapa-kaupunki-field-FI}           ${kaupunki}
    Input Text                          ${lomake-tjpt-toimitustapa-puhelinnumero-field-FI}      ${random-puhnum}