<?php
namespace Loevgaard\DataAwareCommandBundle\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @method string getName() - The command name
 * @method ContainerInterface getContainer()
 */
trait DataAwareCommandTrait
{
    protected $dataFile;

    /**
     * @param \DateTimeInterface|null $dateTime
     */
    protected function setLastRun(\DateTimeInterface $dateTime = null) {
        if(!$dateTime) {
            $dateTime = new \DateTime();
        }
        $this->setCommandData('lastRun', $dateTime);
    }

    /**
     * @return \DateTimeInterface|null
     */
    protected function getLastRun() {
        $lastRun = $this->getCommandData('lastRun');
        if(empty($lastRun)) {
            $lastRun = null;
        }
        return $lastRun;
    }

    /**
     * Sets command data using a key value pair
     *
     * @param string $key
     * @param string $val
     */
    protected function setCommandData($key, $val) {
        $data = $this->getCommandData();

        $data[$key] = $val;
        file_put_contents($this->getDataFile(), serialize($data));
    }

    /**
     * @param string $param
     * @return array
     */
    public function getCommandData($param = null) {
        $dataFile = $this->getDataFile();
        if(!file_exists($dataFile) || !is_readable($dataFile)) {
            return [];
        }

        $data = unserialize(file_get_contents($dataFile));

        if($data === false) {
            return [];
        }

        if($param) {
            return isset($data[$param]) ? $data[$param] : [];
        } else {
            return $data;
        }
    }

    /**
     * @return string
     */
    protected function getDataFile() {
        if(is_null($this->dataFile)) {
            $dataDir = realpath($this->getContainer()->getParameter('loevgaard_data_aware_command.data_dir'));
            if(!is_dir($dataDir) || !is_writable($dataDir)) {
                throw new \RuntimeException('Data directory: '.$dataDir.' either does not exist or is not writable');
            }
            $this->dataFile = $dataDir.'/'.$this->getCanonicalizedCommandName().'.data';
        }

        return $this->dataFile;
    }

    /**
     * Takes any given command name and returns a canonicalized version
     *
     * @return string
     */
    protected function getCanonicalizedCommandName() {
        return preg_replace('/[_]+/', '_', preg_replace('/[^0-9a-zA-Z\-_]+/', '_', $this->getName()));
    }
}