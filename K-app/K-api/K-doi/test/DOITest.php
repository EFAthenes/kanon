<?php
//
//namespace src\test;
//
//use PHPUnit\Framework\TestCase;
//use Exception;
//use src\Helper\DoiContext;
//use src\Services\DoiService;
//use src\Data\Doi;
//use src\Data\Author;
//use src\Data\DoiEvent;
//use src\Data\Institution;
//
//require_once __DIR__ . '/../Helper/DoiContext.php';
//require_once __DIR__ . '/../Services/DoiService.php';
//require_once __DIR__ . '/../Data/Doi.php';
//require_once __DIR__ . '/../Data/Author.php';
//
///**
// * DOI test case.
// */
//class DOITest extends TestCase
//{
//    /**
//     *
//     * @var DOIService
//     */
//    private $dOIServiceTest;
//    private $dOIServiceProd;
//    private $testDoiContext;
//    private $prodDoiContext;
//
//    private $errorMessage;
//
//    /**
//     * Prepares the environment before running a test.
//     */
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        $this->testDoiContext = new DoiContext();
//
//        $this->testDoiContext->setDatacitePrefix("10.80081");
//        $this->testDoiContext->setDataciteUrl("https://api.test.datacite.org");
//        $this->testDoiContext->setDataciteUser("INIST.EFE");
//        $this->testDoiContext->setDatacitePasswd("DoiResEFE%2020!");
//        $this->testDoiContext->setDataciteBaseUrl("https://handle.test.datacite.org/");
//
//
////        $this->prodDoiContext = new DoiContext();
////
////        $this->prodDoiContext->setDatacitePrefix("10.34816");
////        $this->prodDoiContext->setDataciteUrl("https://api.datacite.org");
////        $this->prodDoiContext->setDataciteUser("INIST.EFE");
////        $this->prodDoiContext->setDatacitePasswd("Annex_Regress2baffling");
////        $this->prodDoiContext->setDataciteBaseUrl("https://doi.org/");
//
//        $this->dOIServiceTest = new DOIService($this->testDoiContext);
////        $this->dOIServiceProd = new DOIService($this->prodDoiContext);
//    }
//
//    /**
//     * Cleans up the environment after running a test.
//     */
//    protected function tearDown(): void
//    {
//
//        parent::tearDown();
//    }
//
//    function debug_to_console($data)
//    {
//        $output = $data;
//        if (is_array($output))
//            $output = implode(',', $output);
//
//        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
//    }
//
//    /**
//     * Tests DOI::createDoi()- test site
//     * */
//    public function testCreateDoi() : ?string
//    {
//        //$this->markTestSkipped('already tested');
//        self::expectNotToPerformAssertions();
//        try
//        {
//
//            //demoefa@efa.gr
//            //resefe%2020
//
//            $doi = new Doi();
//
//            $doi->setEfe("casa");
//            $doi->setTitle("toto");
//            $doi->setSuffix("mondoi"); // you are free to define your own suffix
//            $doi->setUrl("ftp://www.persee.fr");
//            $doi->addAuthor(new Author("Bruno", "Morandiere"));
//            $doi->addAuthor(new Author("Antoine", "Morandiere"));
//            $doi->setPublicationYear("2020");
//            $doi->setEmail("bruno.morandiere@resefe.fr");
//            $doi->setInstitution(Institution::casa);
//
//            $returnedDoi = $this->dOIServiceTest->createDoi($doi, DoiEvent::draft);
//
//            return "testCreateDoi : " . $returnedDoi->getDoi() . " : " . $returnedDoi->getState();
//
//        } catch (Exception $e)
//        {
//            $this->errorMessage = "testCreateDoi : " . $e->getMessage();
//        }
//    }
//
//    /**
//     * Tests DOI::updateDoi()- test site
//     * */
//    public function testUpdateDoi() : ?string
//    {
//        // $this->markTestSkipped('already tested');
//        self::expectNotToPerformAssertions();
//        try
//        {
//
//
//            $service = new DOIService($this->testDoiContext);
//            $doiTest = $service->getDoi("mondoi");
//            $doiTest->setTitle("nouveau titre");
//            $returnedDoi = $service->updateDoi($doiTest);
//
//            return  "testUpdateDoi : " . $returnedDoi->getDoi() . " : " . $returnedDoi->getState();
//            assert($returnedDoi->getDoi() !== null);
//            assert($returnedDoi->getState() !== null);
//        } catch (Exception $e)
//        {
//            assert(False, $e->getMessage());
//            $this->errorMessage = "testUpdateDoi : " . $e->getMessage();
//        }
//    }
//
//    /**
//     * Tests DOI::setDoiState()- test site
//     * */
//    public function testSetDoiState() : ?string
//    {
//        // $this->markTestSkipped('already tested');
//        self::expectNotToPerformAssertions();
//        try
//        {
//
//            $doiTest = $this->dOIServiceTest->getDoi("mondoi");
//
//            $returnedDoi = $this->dOIServiceTest->setDoiState($doiTest, DoiEvent::register);
//
//            return "testSetDoiState : " . $returnedDoi->getDoi() . " : " . $returnedDoi->getState();
//            assert($returnedDoi->getDoi() !== null);
//            assert($returnedDoi->getState() !== null);
//        } catch (Exception $e)
//        {
//            assert(False, $e->getMessage());
//            $this->errorMessage = "testSetDoiState : " . $e->getMessage();
//        }
//    }
//
//    /**
//     * Tests DOI::getDoi()
//     * */
//    public function testGetDoi() : ?string
//    {
//        // $this->markTestSkipped('already tested');
//        self::expectNotToPerformAssertions();
//        try
//        {
//
//            $doiTest = $this->dOIServiceTest->getDoi("mondoi"); // doi site de test
//            return "testGetDoi : " . $doiTest->getEmail();
//        } catch (Exception $e)
//        {
//            $this->errorMessage = "testGetDoi : " . $e->getMessage();
//        }
//    }
//
//    /**
//     * Tests DOI::getDoi() - production site
//     */
//    public function testGetDoiProd() : ?string
//    {
//        //  $this->markTestSkipped('already tested');
//        self::expectNotToPerformAssertions();
//        try
//        {
//            $doiProd = $this->dOIServiceProd->getDoi("casa.9db2-0b43"); // doi site de production
//
//            return $doiProd->getEmail();
//        } catch (Exception $e)
//        {
//            $this->errorMessage = "testGetDoiProd : " . $e->getMessage();
//        }
//    }
//
//    /**
//     * Tests DOI::deleteDoi()
//     * */
//    public function testDeleteDoi()
//    {
//        //s  $this->markTestSkipped('must be revisited');
//        self::expectNotToPerformAssertions();
//        try
//        {
//
//            if ($this->dOIServiceTest->deleteDoi("mondoi"))
//            {// doi site de test
//                return "mondoi : supprimé";
//            }
//        } catch (Exception $e)
//        {
//            $this->errorMessage = "testDeleteDoi : " . $e->getMessage();
//        }
//    }
//
//    public function getErrorMessage() : string
//    {
//        return $this->errorMessage;
//    }
//}