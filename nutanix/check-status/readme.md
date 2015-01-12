# Check Status

A couple of simple scripts to check PING responses from multiple IP addresses.

## Usage

- Edit cvm_ips.txt or host_ips.txt (add or remove IP addresses on a separate line, as necessary)
- Run check_cvms.zsh or check_hosts.zsh

## Example Output

	  13:59:06 ...ix/check-status ⭠ master± ./check_cvms.zsh

	Mon 12 Jan 2015 13:59:10 AEDT

	node 10.10.10.30 is up
	node 10.10.10.31 is up
	node 10.10.10.32 is up
	node 10.10.10.33 is up

	  13:59:10 ...ix/check-status ⭠ master± ./check_hosts.zsh

	Mon 12 Jan 2015 13:59:14 AEDT

	node 10.10.10.20 is up
	node 10.10.10.21 is up
	node 10.10.10.22 is up
	node 10.10.10.23 is up

	  13:59:14 ...ix/check-status ⭠ master±

