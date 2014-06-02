<?php
/**
 * @copyright Copyright © 2007, Purodha Blissenabch.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, version 2
 * of the License.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * See the GNU General Public License for more details.
 */
 
/**
 * This extension realizes a new MagicWord {{NUMBEREDHEADINGS}}.
 * If an article contains this MagicWord, numbering of the
 * headings is performed regardless of the user preference setting.
 * 
 * 
 * @author Purodha Blissenbach
 * @author James Hayward
 * @version $Revision: 2.0
 */
 
if (!defined('MEDIAWIKI')) {
	die("This requires the MediaWiki enviroment.");
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MagicNumberedHeadings_NH',
	'version' => '1.2',
	'author' => 'Purodha Blissenbach, James Hayward',
	'url' => 'https://www.mediawiki.org/wiki/Extension:MagicNumberedHeadings',
	'description' => 'Adds MagicWord "<nowiki>{{NUMBEREDHEADINGS}}</nowiki>"',
);
 
$wgExtensionMessagesFiles['MagicNumberedHeadings_NH'] = dirname(__FILE__) . 'MagicNumberedHeadings.i18n.php';
 
$wgHooks['ParserGetVariableValueSwitch'][] = 'MNH_AssignValue';
$wgHooks['MagicWordwgVariableIDs'][] = 'MNH_DeclareVarIds';


function MNH_AssignValue(&$parser, &$cache, &$magicWordId, &$ret) {
	/*if ('MAG_NUMBEREDHEADINGS' == $magicWordId) {
		$parser->mOptions->mNumberHeadings = (TRUE);
		$ret = ' ';
	}*/
	return true;
}

function MNH_DeclareVarIds(&$customVariableIds) {
	$customVariableIds[] = 'MAG_NUMBEREDHEADINGS';
	return true;
}