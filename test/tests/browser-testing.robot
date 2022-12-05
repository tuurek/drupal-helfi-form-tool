*** Settings ***
Documentation         Very simple robot test for testing the robot framework runs.
Metadata              Examplemetadata          This is a simple browser test for ${baseurl}. Metadata is shown in the reports.
Library               Browser
Suite Setup     
Variables             browsertestvariables.yaml

*** Test Cases ***
Example Test
    [Template]  The title must be ${EXPECTED_TITLE} for the url ${URL}
    TitleToBeExpected                  ${TEST_BASEURL}/${pathToTest}

*** Keywords ***
The title should be ${EXPECTED_TITLE} for the url ${URL}
    Set Browser Timeout   10
    Log To Console        \nTesting page: "${URL}" contains "${EXPECTED_TITLE}" as its title.
    New Page              ${URL}
    Get Text              title  ==  ${EXPECTED_TITLE}
