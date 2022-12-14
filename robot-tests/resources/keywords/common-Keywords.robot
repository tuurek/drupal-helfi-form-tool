*** Keywords ***

Log in using suomi.fi authentication - FI
    [Arguments]                         ${hetu}
    Wait Until Page Contains Element    ${Helsinki-Tunnistus-Testi-page-testitunnistaja-button-FI}              20
    Click Element                       ${Helsinki-Tunnistus-Testi-page-testitunnistaja-button-FI}
    Wait Until Page Contains Element    ${Testitunnistaja-page-hetu-field}                                      20
    Input Text                          ${Testitunnistaja-page-hetu-field}                                      ${hetu}
    Click Element                       ${Testitunnistaja-page-tunnistaudu-button-FI}
    Wait Until Page Contains Element    ${Olet-tunnistautumassa-palveluun-jatka-palveluun-button-FI}            20
    Click Element                       ${Olet-tunnistautumassa-palveluun-jatka-palveluun-button-FI}

Accept all cookies
    Sleep                               3
    ${passed} =                         Run Keyword And Return Status           Page Should Contain Element     ${lomake-accept-all-cookie-button}
    Run Keyword If                      ${passed}                               Accept all cookies part 2

Accept all cookies part 2
    Click Element                       ${lomake-accept-all-cookie-button}