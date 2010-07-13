<?php
$default_request = new stdClass();
$default_request->encryptedUserId = 'teacher1';
$default_request->encryptedPassword = 'test';

$encrypt = FALSE;

if($encrypt) {
  $default_request->encryptedUserId = 'WPOtTGinAYwYC1/pIIxZVQ==';
  $default_request->encryptedPassword = 'lPiz/fm4Zhc8aVXfz4EcAg==';
}

/******************
 * TEST COURSE OPERATIONS
 **/

?>
<h1>Course Services</h1>

<?php 
// ------------------
$name = 'getTaughtCourses';
$request = clone $default_request;

//runTest($coursesClient, $name, $request);


// ------------------
$name = 'getClassRoster';
$request = clone $default_request;
$request->siteId = 2;

//runTest($coursesClient, $name, $request);

/*****************
 * TEST FUNCTIONAL CAPABILITIES OPERATIONS
 */

?>
<h1>Functional Capability Services</h1>
<?php 
// ----------------------
$name = 'getFunctionalCapabilities';
$request = clone $default_request;

//runTest($funcClient, $name, $request);


/*********************************
 * TEST GRADES OPERATIONS
 */

?>
<h1>Grades Services</h1>
<?php 

//-------------------------
$name = 'createGradebookItem';
$request = clone $default_request;
$request->siteId = 2;
$request->itemTitle = 'Now Posting';
$request->pointsPossible = 25;

runTest($gradesClient, $name, $request);


//------------------------------
$name = 'listGradebookItems';
$request = clone $default_request;
$request->siteId = 2;

//runTest($gradesClient, $name, $request);

//---------------------------------
$name = 'postIndividualScore';
$request = clone $default_request;
$request->siteId = 2;
$request->itemTitle = 'Now Posting';
$request->deviceId = '12345AA';
$request->pointsEarned = 20.0;
$request->pointsPossible = 25.0;

runTest($gradesClient, $name, $request);

//-------------------------------
$name = 'postIndividualScoreByDto';
$request = clone $default_request;
$request->siteId = 2;
$request->sessionGradeDto = new stdClass();
$request->sessionGradeDto->deviceId = '12345';
$request->sessionGradeDto->itemTitle = 'New Type';
$request->sessionGradeDto->pointsEarned = 5;
$request->sessionGradeDto->pointsPossible = 10.0;

//runTest($gradesClient, $name, $request);

//----------------------------------
$name = 'overrideIndividualScore';
$request = clone $default_request;
$request->siteId = 2;
$request->itemTitle = 'New Type';
$request->deviceId = '12345';
$request->pointsEarned = 1.0;
$request->pointsPossible = 10.0;

//runTest($gradesClient, $name, $request);

//-----------------------------------
$name = 'overrideIndividualScoreByDto';
$request = clone $default_request;
$request->siteId = 2;
$request->sessionGradeDto = new stdClass();
$request->sessionGradeDto->deviceId = '12345';
$request->sessionGradeDto->itemTitle = 'New Type';
$request->sessionGradeDto->pointsEarned = 1.0;
$request->sessionGradeDto->pointsPossible = 10.0;

//runTest($gradesClient, $name, $request);

//-------------------------------------
$name = 'addToIndividualScore';

$request = clone $default_request;
$request->siteId = 2;
$request->itemTitle = 'New Type';
$request->deviceId = '12345';
$request->pointsEarned = 1.0;
$request->pointsPossible = 10.0;

//runTest($gradesClient, $name, $request);

//---------------------------------------
$name = 'addToIndividualScoreByDto';
$request = clone $default_request;
$request->siteId = 2;
$request->sessionGradeDto = new stdClass();
$request->sessionGradeDto->deviceId = '12345';
$request->sessionGradeDto->itemTitle = 'New Type';
$request->sessionGradeDto->pointsEarned = 1.0;
$request->sessionGradeDto->pointsPossible = 10.0;

//runTest($gradesClient, $name, $request);

//----------------------------------------
$name = 'postScores';
$request = clone $default_request;
$request->siteId = 2;

$scores = array();

$dto = new stdClass();
$dto->deviceId = '12345';
$dto->itemTitle = 'New Type';
$dto->pointsEarned = 5.0;
$dto->pointsPossible = 10.0;
$scores[] = $dto;

$dto = new stdClass();
$dto->deviceId = '55555';
$dto->itemTitle = 'New Type';
$dto->pointsEarned = 6.0;
$dto->pointsPossible = 10.0;
$scores[] = $dto;

$dto = new stdClass();
$dto->deviceId = '11111';
$dto->itemTitle = 'New Type';
$dto->pointsEarned = 7.0;
$dto->pointsPossible = 10.0;
$scores[] = $dto;

$request->sessionGradeDtos = $scores;

//runTest($gradesClient, $name, $request);

//-----------------------------
$name = 'postScoresOverrideAll';
$request = clone $default_request;
$request->siteId = 2;

$scores = array();

$dto = new stdClass();
$dto->deviceId = '12346';
$dto->itemTitle = 'New Type';
$dto->pointsEarned = 5.0;
$dto->pointsPossible = 10.0;
$scores[] = $dto;

$dto = new stdClass();
$dto->deviceId = '55555';
$dto->itemTitle = 'New Type';
$dto->pointsEarned = 1.0;
$dto->pointsPossible = 10.0;
$scores[] = $dto;

$dto = new stdClass();
$dto->deviceId = '11111';
$dto->itemTitle = 'New Type';
$dto->pointsEarned = 2.0;
$dto->pointsPossible = 10.0;
$scores[] = $dto;

$request->sessionGradeDtos = $scores;

//runTest($gradesClient, $name, $request);

?>