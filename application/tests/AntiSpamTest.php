<?php

class AntiSpamTest extends Unittest_TestCase
{
  /**
   * Test strings for spam comments.
   * These comments definitely should be blocked.
   **/
  public function providerSpamComments()
  {
    return array(
      array('Покупайте наших <b>слонов!</b> <a href="http://example.com">Здесь.</a>'),
      array('Градостроительная гаубица ляпапам на черепах. Только свежие цены! Заходите на www.dj.example.com!'),
      array('ABC Technologies announces the beginning of a new unprecendented global campaign. Please sign at out site.'),
    );
  }
 
   /**
    * Test strings for legit comments.
    * These comments may be passed.
    **/
  public function providerLegitComments()
  {
    return array(
      array('На самом деле, незастеклённый балкон - это очень-очень плохо. У нас вот на удочку белье снимали соседи сверху, пока не застеклили всё. Мы на это так вовремя решились.'),
      array('Между прочим, у меня ещё работа есть.'),
    );
  }

  /**
   * @dataProvider providerSpamComments
   * @group antispam
   */
  function testAntiSpamCheckSpam($content)
  {
    $this->assertEquals(Model_Comment::antispam_check($content), FALSE);
  }

  /**
   * @dataProvider providerLegitComments
   * @group antispam
   */
  function testAntiSpamCheckLegit($content)
  {
    $this->assertEquals(Model_Comment::antispam_check($content), TRUE);
  }
}
