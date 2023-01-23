<?php

namespace MediaWiki\Auth;

use MediaWiki\MediaWikiServices;
use User;
use Wikimedia\Rdbms\IDatabase;

/**
 * Hackspace Management System authorisation plugin
 * Allow hackspace members to use to use their HMS account to login on the wiki
 *
 * @file
 * @ingroup   Extensions
 * @version   0.1.0
 * @author    Daniel Swann, Matt Lloyd
 * @copyright © 2023 Daniel Swann, Matt Lloyd
 * @licence   MIT
 */
class HmsPasswordPrimaryAuthenticationProvider extends AbstractPrimaryAuthenticationProvider
{
    protected $hms_url;
    protected $secret;
    protected $salt;
    protected $debug = false;
    protected $log_file;

    private $uname = '';
    private $realname = '';
    private $email = '';

    public function __construct($params = [])
    {
        $this->writeMsg('__construct');
        \Hooks::register('UserLoggedIn', [$this, 'onUserLoggedIn']);

        $this->hms_url  = $params['hms_url'];
        $this->secret   = $params['secret'];
        $this->salt     = $params['salt'];
        $this->debug    = $params['debug'];
        $this->log_file = $params['log_file'];
    }

    public function getAuthenticationRequests($action, array $options)
    {
        switch ($action) {
            case AuthManager::ACTION_LOGIN:
                return [ new PasswordAuthenticationRequest() ];
            default:
                return [];
        }
    }

    public function beginPrimaryAuthentication(array $reqs)
    {
        $req = AuthenticationRequest::getRequestByClass($reqs, PasswordAuthenticationRequest::class);
        if (! $req) {
            return AuthenticationResponse::newAbstain();
        }

        $this->writeMsg("beginPrimaryAuthentication( $req->username, <password> )");

        if ($this->check_password($req->username, $req->password)) {
            return AuthenticationResponse::newPass($req->username);
        } else {
            return AuthenticationResponse::newAbstain();
        }
    }

    public function beginPrimaryAccountCreation($user, $creator, array $reqs)
    {
        return AuthenticationResponse::newFail();
    }

    public function accountCreationType()
    {
        return self::TYPE_NONE;
    }

    public function providerAllowsAuthenticationDataChange(AuthenticationRequest $req, $checkData = true)
    {
        $this->writeMsg('providerAllowsAuthenticationDataChange()');

        $v = var_export($req, true);
        $this->writeMsg($v);


        if ($req->username === null) {
            global $wgUser;
            $username = trim($wgUser->getName());
        } else {
            $username = $req->username;
        }

        if ($this->is_local_user($username)) {
            $this->writeMsg('providerAllowsAuthenticationDataChange(): newGood/ignored (2)');
            return \StatusValue::newGood('ignored');
        } else {
            // HMS user - don't allow password change.
            $this->writeMsg('providerAllowsAuthenticationDataChange(): newFatal');

            return \StatusValue::newFatal('authmanager-authplugin-setpass-denied');
        }
    }

    /* Test if $username is a local user. Assume if the user exists in the mediawiki database *
     * and had a valid password set, that they're local                                       */
    private function is_local_user($username)
    {
        $user = User::newFromName($username, 'creatable');
        if ($user == false) { // means the username is invalid (not just doesn't exist)
            $this->writeMsg("is_local_user> invalid username: $username");

            return false;
        }
        $user->load();

        if ($user->mId == '0') { // user doesn't exist
            $this->writeMsg("is_local_user> user doesn't exist");

            return false;
        }

        $row = wfgetdb(DB_MASTER)->selectRow('user', 'user_password', [ 'user_name' => $user->getName() ]);
        if ($row->user_password == '#') {
            $this->writeMsg("is_local_user> invalid local password - user isn't local");

            return false;
        } else {
            $this->writeMsg('is_local_user> user IS local (non-HMS)');

            return true;
        }
    }

    public function providerChangeAuthenticationData(AuthenticationRequest $req)
    {
        return;
    }

    public function testUserExists($username, $flags = User::READ_NORMAL)
    {
        $this->writeMsg("userExists($username)");

        return true;
    }

    public function onUserLoggedIn($user)
    {
        $this->writeMsg("onUserLoggedIn('" . $user->getName() . "')");
        if ($user->getName() == $this->uname) {
            $user->setRealName($this->realname);
            $user->setEmail($this->email);
            $user->confirmEmail();

            wfgetdb(DB_MASTER)->update(
                'user',
                [ 'user_password' => '#' ],
                [ 'user_name' => $user->getName() ],   /* invalid password hash - i.e. no local password */
                __METHOD__
            );

            $user->saveSettings();
            $this->writeMsg('onUserLoggedIn saved settings');
        }
    }

    private function check_password($username, $password)
    {
        $data = [
            'function'=>'login',
            'username'=>$username,
            'password'=>$password
        ];

        $result = $this->hms_query($data);

        $granted = false;
        if ($result != false
            && isset($result['access_granted'])
            && $result['access_granted'] == true) {
                $granted = true;
                $this->realname = $result['name'];
                $this->email = $result['email'];
                $this->uname = $username;
        }

        if ($granted) {
            $this->writeMsg("check_password($username) => Access granted");
        } else {
            $this->writeMsg("check_password($username) => Access denied");
        }

        return $granted;
    }

    private function hms_query($data)
    {
        $data['hash']  = $this->secret;
        $data['hash'] = crypt(json_encode($data), $this->salt);
        $query_string = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->hms_url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4); // 4 second timeout
    //  curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/RapidSSL_CA_bundle.pem');
    //  curl_setopt($ch, CURLOPT_CAINFO, '/home/nottinghack/public_wiki/extensions/HMSAuth/cacert.pem');
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $result = curl_exec($ch);
        if ($result == false) {
            $this->writeMsg('hms_query> curl_exec failed!');
            $this->writeMsg('curl_error=' . curl_error($ch));

            return false;
        }
        $res = json_decode($result, true);
        curl_close($ch);

        $this->writeMsg('hms_query> result = [' . print_r($res, true));

        return $res;
    }

    private function writeMsg($sMsg)
    {
        if ($this->debug) {
            $sOutput = date('M d H:i:s') . ': ' . $sMsg . '\n';
            file_put_contents($this->log_file, $sOutput, FILE_APPEND);
        }
    }
}
