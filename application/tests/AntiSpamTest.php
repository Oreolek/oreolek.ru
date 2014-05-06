<?php
/*
 *  Personal site oreolek.ru source code
 *  Copyright (C) 2014 Alexander Yakovlev
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */

/**
 * Antispam test for comment automoderation.
 **/
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
