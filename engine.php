<?php
  if(isset($_GET['term'])){
    $_SESSION['term'] = addslashes($_GET['term']);
  }
?>
    <center>
    <?php
      ##########################################################################
      #VAR SETUP
      ##########################################################################
      $utils = "http://www.ncbi.nlm.nih.gov/entrez/eutils"; #site to pull from
      $term = "test";
      $retMode = "xml";
      $report = "abstract"; #Type of info to return
      $retstart = 0;        #Where to start
      $retmax = 10;         #Max Results to Return
      $db = "pubmed";       #pubmed, pmc, nlmc, journals, omim, taxon, snp, gene, seq
      $count = 0;           #How many results are returned, gathered from initial XML
      $queryKey = "";       #gathered from initial XML
      $webEnv = "";         #gathered from initial XML

      ##########################################################################
      #FORM
      ##########################################################################
      ?>

    <form action="index.php" method="get">
      <b>Query Term:</b> <input type="textfield" name="term" value="<?php echo $_SESSION['term']; ?>">
      <input type="submit" value="Search" class="formbutton">
    </form>
    <hr>
    <b>
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=pubmed">PUBMED</a> |
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=pmc">PMC</a> |
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=nlmcatalog">NLM Catalog</a> |
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=journals">Journals</a> |
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=omim">Omim</a> |
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=taxonomy">Taxonomy</a> |
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=snp">SNP</a> |
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=gene">Gene</a> |
    <a href="index.php?term=<?php echo $_SESSION['term']; ?>&db=sequences">Sequence</a>
    </b>
    <hr>
    </center>


      <?
      ##########################################################################
      #INITIAL XML
      ##########################################################################
      $db = $_GET['db'] ? $_GET['db'] : "pubmed";
      $term = $_GET['term'] ? $_GET['term'] : "test";

      $initXML = $utils . "/esearch.fcgi?db=" . $db .
        "&retmax=1&usehistory=y&term=" . $term;
      $esearch_result = simplexml_load_file($initXML);

      ##########################################################################
      #SETTING VARS FROM INITIAL XML
      ##########################################################################
      $count = $esearch_result->Count;
      $queryKey = $esearch_result->QueryKey;
      $webEnv = $esearch_result->WebEnv;

      #DEBUG INFO
      debug($count);
      debug($queryKey);
      debug($webEnv);

      ##########################################################################
      #SETUP TO PULL RESULTS
      ##########################################################################
      $efetch = $utils . "/efetch.fcgi?" .
        "rettype=" . $report.
        "&retmode=" . $retMode .
        "&retstart=" . $retstart .
        "&retmax=" . $retmax .
        "&db=" . $db .
        "&query_key=" . $queryKey .
        "&WebEnv=" . $webEnv;

      debug($efetch);
      echo "<b><i>" . $_GET['db'] . " Results:</i></b><hr>";
      $results = simplexml_load_file($efetch);
      #echo "After Results";

      #var_dump($results);
      #foreach($results->PubmedArticle as $Article){
      #  echo "hmms<br />";
      #}
      #$pattern = "/\n/";
      #$replacement = "<br />";

      #preg_replace($pattern, $replacement, $results);
      //debug($results);

      echo parse_xml($results, $db);

    ?>

    <?php
      ##########################################################################
      #FUNCTIONS
      ##########################################################################

      ##########################################################################
      #DEBUGGING
      ##########################################################################
      function debug($var){
        if($_GET['debug'] == 1){
          echo "<b><i>" . $var . "</i></b><br />";
        }
      }

      ##########################################################################
      #PARSING VARIOUS XML FORMATS
      ##########################################################################
      function parse_xml($xml, $db){
        $html = "";

        switch ($db){
          case "pubmed":
            foreach ($xml->PubmedArticle as $pubmedArticle){
              $html .= "<h3><a href=\"http://www.ncbi.nlm.nih.gov/pubmed/" . $pubmedArticle->MedlineCitation->PMID . "\">"
                    . strtoupper($pubmedArticle->MedlineCitation->Article->ArticleTitle) . "</a></h3>";
              $html .= substr($pubmedArticle->MedlineCitation->Article->Abstract->AbstractText, 0, 500) . "...";
              $html .= "<br />";
              #Publishing Info
              $html .= "<b>Journal:</b> ";
              $html .= $pubmedArticle->MedlineCitation->Article->Journal->Title
                    . "<b> : Vol. </b>"
                    . $pubmedArticle->MedlineCitation->Article->Journal->JournalIssue->Volume
                    . "<b>  Iss. </b>"
                    . $pubmedArticle->MedlineCitation->Article->Journal->JournalIssue->Issue;
              $html .= "<hr><br /><br /><br />";
            }
            break;

          case "pmc":
            foreach ($xml->article as $pmcArticle){
              $html .= "<h3><a href=\"http://www.ncbi.nlm.nih.gov/pmc/articles/PMC" . $pmcArticle->front->{'article-meta'}->{'article-id'} . "\">"
                    . strtoupper($pmcArticle->front->{'article-meta'}->{'title-group'}->{'article-title'}) . "</a></h3>";
              $html .= substr($pmcArticle->front->{'article-meta'}->abstract->p, 0, 500) . "...";
              $html .= "<br />";
              #Publishing Info
              $html .= "<b>Journal:</b> ";
              $html .= $pmcArticle->front->{'journal-meta'}->{'journal-title'}
                    . "<b> : Vol. </b>"
                    . $pmcArticle->front->{'article-meta'}->volume
                    . "<b>  Iss. </b>"
                    . $pmcArticle->front->{'article-meta'}->issue;
              $html .= "<hr><br /><br /><br />";
            }
            break;
          case "nlmcatalog":
            foreach ($xml->NLMCatalogRecord as $record){
              $html .= "<h3><a href=\"http://locatorplus.gov/cgi-bin/Pwebrecon.cgi?DB=local&v1=1&ti=1,1&Search_Arg=" . $record->NlmUniqueID . "&Search_Code=0359&CNT=20&SID=1\">"
                    . strtoupper($record->TitleMain->Title) . "</a></h3>";
              $html .= substr($record->ContentsNote, 0, 500) . "...";
              $html .= "<br />";
              #Publishing Info
              $html .= "<b>Publication Type:</b> ";
              $html .= $record->PublicationTypeList->PublicationType .
                      "<b> Copyright: </b>" .
                      $record->PublicationInfo->Imprint;
              $html .= "<hr><br /><br /><br />";
            }
            break;
          case "journals":
            foreach ($xml->Serial as $serial){
              $html .= "<h3><a href=\"http://locatorplus.gov/cgi-bin/Pwebrecon.cgi?DB=local&v1=1&ti=1,1&Search_Arg=" . $serial->NlmUniqueID . "&Search_Code=0359&CNT=20&SID=1\">"
                    . strtoupper($serial->Title) . "</a></h3>";
              $html .= $serial->SortSerialName;
              $html .= "<br />";
              #Publishing Info
              $html .= "<b>Publisher:</b> ";
              $html .= $serial->PublicationInfo->Publisher .
                      "<b> Year: </b>" .
                      $serial->PublicationInfo->PublicationFirstYear;
              $html .= "<hr><br /><br /><br />";
            }
            break;
          case "omim":
            foreach ($xml->{'Mim-entry'} as $mimentry){
              $html .= "<h3><a href=\"http://www.ncbi.nlm.nih.gov/entrez/dispomim.cgi?id=" . $mimentry->{'Mim-entry_mimNumber'} . "\">"
                    . strtoupper($mimentry->{'Mim-entry_title'}) . "</a></h3>";
              #Publishing Info
              $html .= "<b>Copyright:</b> "
                    . $mimentry->{'Mim-entry_copyright'};
              $html .= "<hr><br /><br /><br />";
            }
            break;
          case "taxonomy":
            $html .= "<h3>NO RESULTS</h3>";
            break;
          case "snp":
            foreach ($xml->Rs as $rs){
              #echo "boo";
              #echo $rs->attributes()->rsId;
              #echo xml_attribute($rs, "rsId");
              $html .= "<h3><a href=\"http://www.ncbi.nlm.nih.gov/projects/SNP/snp_ref.cgi?rs=" . $rs->attributes()->rsId . "\">"
                    . strtoupper($rs->Sequence->attributes()->exemplarSs) . "</a></h3>";
              $html .= substr($rs->Sequence->Seq5, 0, 50) . "...";
              $html .= "<hr><br /><br /><br />";
            }
            break;
          case "gene":
            foreach ($xml->Entrezgene as $eg){
              #echo "boo";
              #echo $rs->attributes()->rsId;
              #echo xml_attribute($rs, "rsId");
              $html .= "<h3><a href=\"http://www.ncbi.nlm.nih.gov/gene/" . $eg->{'Entrezgene_track-info'}->{'Gene-track'}->{'Gene-track_geneid'} . "\">"
                    . strtoupper($eg->{'Entrezgene_track-info'}->{'Gene-track'}->{'Gene-track_geneid'}) . "</a></h3>";
              $html .= substr($eg->Entrezgene_source->BioSource->BioSource_org->{'Org-ref'}->{'Org-ref_taxname'}, 0, 50) . "...";
              $html .= "<hr><br /><br /><br />";
            }
            break;
          case "sequences":
            $html .= "<h3>NO RESULTS</h3>";
            break;
          default:
            $html .= "<h3>INVALID DATABASE</h3>";
            break;
        }
        return $html;
      }
    ?>
