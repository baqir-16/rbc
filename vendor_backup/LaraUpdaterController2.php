<?php
/*
* @author: Pietro Cinaglia
* 	.website: http://linkedin.com/in/pietrocinaglia
*/
namespace pcinaglia\laraUpdater;

use App\Http\Controllers\Controller;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Artisan;
use Auth;
use URL;
use Exception;

class LaraUpdaterController extends Controller
{
	private $tmp_backup_dir = null;
	private $update_delete_file = 'udf.txt';
    private $update_clean_dir = 'ucd.txt';
    private $update_delete_dir = 'udd.txt';

	private function checkPermission(){

    	if( config('laraupdater.allow_users_id') !== null ){

			// 1
			if( config('laraupdater.allow_users_id') === false ) return true;

			// 2
			if( in_array(Auth::User()->id, config('laraupdater.allow_users_id')) === true ) return true;
		}

		return false;
	}

	/*
	* Download and Install Update.
	*/
	public function update()
	{
		echo "<h2>LaraUpdater</h2>";
		echo '<h4><a href="./system_update">Return to App HOME</a></h4>';

		if( ! $this->checkPermission() ){
			echo "ACTION NOT ALLOWED.";
			exit;
		}

		$lastVersionInfo = $this->getLastVersion();

		if ( $lastVersionInfo['version'] <= $this->getCurrentVersion() ){
			echo '<p>&raquo; Your System IS ALREADY UPDATED to last version !</p>';
			exit;
		}

		try{
			$this->tmp_backup_dir = base_path().'/backup_'.date('Ymd');

			echo '<p>UPDATE FOUND: '.$lastVersionInfo['version'].' <i>(current version: '.$this->getCurrentVersion().')</i></p>';
			echo '<p>DESCRIPTION: <i>'.$lastVersionInfo['description'].'</i></p>';

            $db_backup_status = Artisan::call('backup:mysql-dump'); //create a backup of db, backup stored in "/storage/app/backups"
            if(isset($db_backup_status))
                echo '<p>&raquo; Database Backup  ........... [OK!!!]</p>';
            else
                echo '<p>&raquo; Database Backup  ........... [FAILED!!!]</p>';

			echo '<p>&raquo; Update, downloading ........... ';

			$update_path = null;
			if( ($update_path = $this->download($lastVersionInfo['archive'])) === false)
				throw new \Exception("Error during download.");

			echo '[OK] </p>';

			Artisan::call('down');
			echo '<p>&raquo; SYSTEM Mantence Mode => ON</p>';

			$newFiles = $this->getNewFiles($update_path, $lastVersionInfo['archive']);
			$status = $this->install($lastVersionInfo['version'], $update_path, $lastVersionInfo['archive']);

			if($status){
				$this->setCurrentVersion($lastVersionInfo['version']); //update system version
				Artisan::call('up'); //restore system UP status
				echo '<p>&raquo; SYSTEM Mantence Mode => OFF</p>';
				echo '<p class="success">SYSTEM IS NOW UPDATED TO VERSION: '.$lastVersionInfo['version'].'</p>';
			}else{
                echo '<p>Error during updating.';
                $this->restore($newFiles, $db_backup_status);
                echo '</p>';
            }

		}catch (\Exception $e) {
		    var_dump($e);
			echo '<p>ERROR DURING UPDATE (!!check the update archive!!) --TRY to restore OLD status ........... ';
			$this->restore($newFiles, $db_backup_status);
			echo '</p>';
		}
	}

    public function getNewFiles($update_path, $archive)
    {
        try{
            $newFiles = [];
            $zipHandle = zip_open($update_path);
            $archive = substr($archive,0, -4);

            while ($zip_item = zip_read($zipHandle)){
                $filename = zip_entry_name($zip_item);
                $dirname = dirname($filename);

                // Exclude update files
                if(basename($filename) == $this->update_delete_file)
                    continue;
                elseif(basename($filename) == $this->update_clean_dir)
                    continue;
                elseif(basename($filename) == $this->update_delete_dir)
                    continue;

                // Exclude these cases (1/2)
                if(	substr($filename,-1,1) == '/' || dirname($filename) === $archive || substr($dirname,0,2) === '__') continue;

                //Exclude root folder (if exist)
                if( substr($dirname,0, strlen($archive)) === $archive )
                    $dirname = substr($dirname, (strlen($dirname)-strlen($archive)-1)*(-1));

                // Exclude these cases (2/2)
                if($dirname === '.' ) continue;

                $filename = $dirname.'/'.basename($filename); //set new purify path for current file

                if ( !is_dir(base_path().'/'.$filename) ){
                    $contents = zip_entry_read($zip_item, zip_entry_filesize($zip_item));
                    $contents = str_replace("\r\n", "\n", $contents);

                    if(!File::exists(base_path().'/'.$filename))
                        array_push($newFiles, $filename);

                    unset($contents);
                }
            }
            zip_close($zipHandle);
        }catch (\Exception $e) { return false; }

        return $newFiles;
    }

	private function install($lastVersion, $update_path, $archive)
	{
		try{
			$execute_commands = false;
			$upgrade_cmds_filename = 'upgrade.php';
			$upgrade_cmds_path = config('laraupdater.tmp_path').'/'.$upgrade_cmds_filename;

			$zipHandle = zip_open($update_path);
			$archive = substr($archive,0, -4);

			echo '<p>CHANGELOG: </p>';
			echo '<ul>';

            while ($zip_item = zip_read($zipHandle)){
                $filename = zip_entry_name($zip_item);
                $dirname = dirname($filename);

                // Exclude these cases (1/2)
                if(	substr($filename,-1,1) == '/' || dirname($filename) === $archive || substr($dirname,0,2) === '__') continue;

                //Exclude root folder (if exist)
                if( substr($dirname,0, strlen($archive)) === $archive )
                    $dirname = substr($dirname, (strlen($dirname)-strlen($archive)-1)*(-1));

                // Exclude these cases (2/2)
                if($dirname === '.' ) continue;

                $filename = $dirname.'/'.basename($filename); //set new purify path for current file

                if ( strpos($filename, $this->update_delete_file) !== false ) {
                    $contents = zip_entry_read($zip_item, zip_entry_filesize($zip_item));
                    if(strlen($contents) == 0) continue;

                    $arr = explode("\n", $contents);
                    foreach ($arr as $file){
                        File::delete(base_path().$file);
                    }
                }

                if ( strpos($filename, $this->update_clean_dir) !== false ) {
                    $contents = zip_entry_read($zip_item, zip_entry_filesize($zip_item));
                    if(strlen($contents) == 0) continue;

                    $arr = explode("\n", $contents);
                    foreach ($arr as $dir){
                        File::cleanDirectory(base_path().$dir);
                    }
                }

                if ( strpos($filename, $this->update_delete_dir) !== false ) {
                    $contents = zip_entry_read($zip_item, zip_entry_filesize($zip_item));
                    if(strlen($contents) == 0) continue;

                    $arr = explode("\n", $contents);
                    foreach ($arr as $dir){
                        File::deleteDirectory(base_path().$dir);
                    }
                }
            }
            zip_close($zipHandle);

            $zipHandle = zip_open($update_path);
			while ($zip_item = zip_read($zipHandle)){
				$filename = zip_entry_name($zip_item);
				$dirname = dirname($filename);

				// Exclude update files
				if(basename($filename) == $this->update_delete_file)
                    continue;
				elseif(basename($filename) == $this->update_clean_dir)
                    continue;
				elseif(basename($filename) == $this->update_delete_dir)
                    continue;

				// Exclude these cases (1/2)
				if(	substr($filename,-1,1) == '/' || dirname($filename) === $archive || substr($dirname,0,2) === '__') continue;
				
				//Exclude root folder (if exist)
				if( substr($dirname,0, strlen($archive)) === $archive )
					$dirname = substr($dirname, (strlen($dirname)-strlen($archive)-1)*(-1));

				// Exclude these cases (2/2)
				if($dirname === '.' ) continue;

				$filename = $dirname.'/'.basename($filename); //set new purify path for current file

				if ( !is_dir(base_path().'/'.$dirname) ){ //Make NEW directory (if exist also in current version continue...)
					File::makeDirectory(base_path().'/'.$dirname, $mode = 0755, true, true);
					echo '<li>Directory => '.$dirname.'[ OK ]</li>';
				}

				if ( !is_dir(base_path().'/'.$filename) ){ //Overwrite a file with its last version
					$contents = zip_entry_read($zip_item, zip_entry_filesize($zip_item));
					$contents = str_replace("\r\n", "\n", $contents);
					if ( strpos($filename, 'upgrade.php') !== false ) {
						File::put($upgrade_cmds_path, $contents);

						$execute_commands = true;
                    }else{
						echo '<li>File => '.$filename.' ........... ';

						if(File::exists(base_path().'/'.$filename))
						    $this->backup($filename); //backup current version

						File::put(base_path().'/'.$filename, $contents);

						unset($contents);
						echo' [ OK ]'.'</li>';
					}
				}
			}

			zip_close($zipHandle);
			echo '</ul>';

            Artisan::call('migrate');

			if($execute_commands == true){
				include ($upgrade_cmds_path);

				if(main()) //upgrade-VERSION.php contains the 'main()' method with a BOOL return to check its execution.
					echo '<p class="success">&raquo; Commands successfully executed.</p>';
				else
					echo '<p class="danger">&raquo; Error during commands execution.</p>';

				unlink($upgrade_cmds_path);
				File::delete($upgrade_cmds_path); //clean TMP
			}
			File::delete($update_path); //clean TMP
			File::deleteDirectory($this->tmp_backup_dir); //remove backup temp folder
//            File::cleanDirectory(config('laraupdater.db_backup_path')); //delete all files in db backup folder
		}catch (\Exception $e) { return false; }

		return true;
	}

	/*
	* Download Update from $update_baseurl to $tmp_path (local folder).
	*/
	private function download($update_name)
	{
		try{
			$filename_tmp = config('laraupdater.tmp_path').'/'.$update_name;

			if ( !is_file( $filename_tmp ) ) {
				$newUpdate = file_get_contents(config('laraupdater.update_baseurl').'/'.$update_name);

				$dlHandler = fopen($filename_tmp, 'w');
				if ( !fwrite($dlHandler, $newUpdate) ){
					echo '<p>Could not save new update (check tmp/ write permission). Update aborted.</p>';
					exit();
				}
			}

		}catch (\Exception $e) { return false; }

		return $filename_tmp;
	}

	/*
	* Return current version (as plain text).
	*/
	public function getCurrentVersion(){
		$version = File::get(base_path().'/version.txt');
		return $version;
	}

	/*
	* Check if a new Update exist.
	*/
	public function check()
	{
		$lastVersionInfo = $this->getLastVersion();
		if ( $lastVersionInfo['version'] > $this->getCurrentVersion() )
			return $lastVersionInfo['version'];
		
		return '';
	}

	private function setCurrentVersion($last){
		File::put(base_path().'/version.txt', $last); //UPDATE $current_version to last version
	}

	private function getLastVersion(){
		$content = file_get_contents(config('laraupdater.update_baseurl').'/laraupdater.json');
		$content = json_decode($content, true);
		return $content; //['version' => $v, 'archive' => 'RELEASE-$v.zip', 'description' => 'plain text...'];
	}

	private function backup($filename){
		$backup_dir = $this->tmp_backup_dir;

		if ( !is_dir($backup_dir) ) File::makeDirectory($backup_dir, $mode = 0755, true, true);
		if ( !is_dir($backup_dir.'/'.dirname($filename)) ) File::makeDirectory($backup_dir.'/'.dirname($filename), $mode = 0755, true, true);

		File::copy(base_path().'/'.$filename, $backup_dir.'/'.$filename); //to backup folder
	}

	private function restore($newFiles, $db_backup_status){
	    if($newFiles != false) {
            foreach ($newFiles as $file) {
                File::delete(base_path() . '/' . $file);
            }
        }

		if( !isset($this->tmp_backup_dir) )
			$this->tmp_backup_dir = base_path().'/backup_'.date('Ymd');

		try{
			$backup_dir = $this->tmp_backup_dir;
			$backup_files = File::allFiles($backup_dir);

			foreach ($backup_files as $file){
				$filename = (string)$file;
				$filename = substr($filename, (strlen($filename)-strlen($backup_dir)-1)*(-1));
				echo $backup_dir.'/'.$filename." => ".base_path().'/'.$filename;
				File::copy($backup_dir.'/'.$filename, base_path().'/'.$filename); //to respective folder
			}

            if(isset($db_backup_status)){
                $exec_commands = "cd ..
                    php artisan backup:mysql-restore --restore-latest-backup --yes 2>&1";
                exec($exec_commands, $output);
            }

            Artisan::call('up'); //restore system UP status
            echo '<p>&raquo; SYSTEM Mantence Mode => OFF</p>';

		}catch(\Exception $e) {
			echo "Exception => ".$e->getMessage();
			echo "<BR>[ FAILED ]";
			echo "<BR> Backup folder is located in: <i>".$backup_dir."</i>.";
			echo "<BR> Database backup SQL file is located in: <i>".config('laraupdater.db_backup_path')."</i>.";
			echo "<BR> Remember to restore System UP-Status through shell command: <i>php artisan up</i>.";
			return false;
		}

        File::deleteDirectory($this->tmp_backup_dir); //remove backup temp folder
		echo "[ RESTORED ]";
		return true;
	}
}
