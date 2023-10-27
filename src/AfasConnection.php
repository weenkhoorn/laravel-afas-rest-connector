<?php

namespace WeSimplyCode\LaravelAfasRestConnector;

class AfasConnection
{
    /**
     * The configuration of the selected connection
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a getConnector for the selected connection
     * @param string $name
     * @param bool $jsonFilter
     * @return AfasGetConnector
     */
    public function getConnector(string $name, bool $jsonFilter = false, string $token = null, string $environment = null): AfasGetConnector
    {
        if (!array_key_exists($name, $this->config['getConnectors'])) {
            throw new \InvalidArgumentException("GetConnector $name is not configured for this connection.");
        }

        $config = $this->config['getConnectors'][$name];

        if($token && $environment) {
            $config['token'] = $token;
            $config['environment'] = $environment;
        }

        return new AfasGetConnector($this, $config, $jsonFilter);
    }

    /**
     * Get a updateConnector for the selected connection
     * @param string $name
     * @return AfasUpdateConnector
     */
    public function updateConnector(string $name, string $token = null, string $environment = null): AfasUpdateConnector
    {
        if (!array_key_exists($name, $this->config['updateConnectors'])) {
            throw new \InvalidArgumentException("UpdateConnector $name is not configured for this connection.");
        }

        $config = $this->config['updateConnectors'][$name];

        if($token && $environment) {
            $config['token'] = $token;
            $config['environment'] = $environment;
        }
        
        return new AfasUpdateConnector($this, $config);
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->config['token'];
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        if (!$env = $this->config['environment'])
        {
            throw new \Exception("No Afas environment set for selected connection.");
        }

        return $env;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getTypeOfEnvironment(): string
    {
        switch (substr($this->getEnvironment(), 0, 1))
        {
            case "O":
                $type = "production";
                break;
            case "T":
                $type = "test";
                break;
            case "A":
                $type = "accept";
                break;
            default:
                throw new \Exception("Could not extract the type of the environment. Please check the environment of the selected connection.");
        }

        return $type;
    }

    public function getEnvironmentNumbers()
    {
        $env = $this->getEnvironment();

        return filter_var($env, FILTER_SANITIZE_NUMBER_INT);
    }
}
