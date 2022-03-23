<?php

namespace App\Console\Commands;

use App\Events\TunnelCreated;
use Illuminate\Console\Command;
use JnJairo\Laravel\Ngrok\NgrokCommand as NgrokDefaultCommand;
use Symfony\Component\Process\Process;

class NgrokCommand extends NgrokDefaultCommand
{
    protected $signature = 'ngrok_event
                            {host-header? : Host header to identify the app (Example: myapp.test)}
                            {--H|host= : Host to tunnel the requests (default: localhost)}
                            {--P|port= : Port to tunnel the requests (default: 80)}
                            {--E|extra=* : Extra arguments to ngrok command}';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
    {
        $hostHeader = $this->argument('host-header');
        $host = $this->option('host');
        $port = $this->option('port');
        $extra = $this->option('extra');

        if ($hostHeader === null) {
            $url = $this->getLaravel()->make('config')->get('app.url');

            $urlParsed = parse_url($url);

            if ($urlParsed !== false) {
                if (isset($urlParsed['host'])) {
                    $hostHeader = $urlParsed['host'];
                }

                if (isset($urlParsed['port']) && $port === null) {
                    $port = $urlParsed['port'];
                }
            }
        }

        if (empty($hostHeader)) {
            $this->error('Invalid host header');
            return 1;
        }

        $host = $host ?: 'localhost';
        $port = $port ?: '80';

        $this->line('-----------------');
        $this->line('|     NGROK     |');
        $this->line('-----------------');

        $this->line('');

        $this->line('<fg=green>Host header: </fg=green>' . $hostHeader);
        $this->line('<fg=green>Host: </fg=green>' . $host);
        $this->line('<fg=green>Port: </fg=green>' . $port);

        if (! empty($extra)) {
            $this->line('<fg=green>Extra: </fg=green>' . implode(' ', $extra));
        }

        $this->line('');

        $process = $this->processBuilder->buildProcess($hostHeader, $port, $host, $extra);

        return $this->runProcess($process);
    }

    /**
     * Run the process.
     *
     * @param \Symfony\Component\Process\Process $process
     * @return int Exit code.
     */
    protected function runProcess(Process $process) : int
    {
        $webService = $this->webService;

        $webServiceStarted = false;
        $tunnelStarted = false;

        $process->run(function ($type, $data) use (&$process, &$webService, &$webServiceStarted, &$tunnelStarted) {
            if (! $webServiceStarted) {
                if (preg_match('/msg="starting web service".*? addr=(?<addr>\S+)/', $process->getOutput(), $matches)) {
                    $webServiceStarted = true;

                    $webServiceUrl = 'http://' . $matches['addr'];

                    $webService->setUrl($webServiceUrl);

                    $this->line('<fg=green>Web Interface: </fg=green>' . $webServiceUrl . "\n");
                }
            }

            if ($webServiceStarted && ! $tunnelStarted) {
                $tunnels = $webService->getTunnels();

                if (! empty($tunnels) && count($tunnels) > 1) {
                    event(new TunnelCreated($tunnels));

                    $this->line('<fg=green>Tunnels created! </fg=green>');

                    $tunnelStarted = true;

                    foreach ($tunnels as $tunnel) {
                        $this->line('<fg=green>Forwarding: </fg=green>'
                            . $tunnel['public_url'] . ' -> ' . $tunnel['config']['addr']);
                    }
                }
            }

            if (Process::OUT === $type) {
                $process->clearOutput();
            } else {
                $this->error($data);
                $process->clearErrorOutput();
            }
        });

        $this->error($process->getErrorOutput());

        return $process->getExitCode();
    }
}
