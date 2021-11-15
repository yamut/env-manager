# .env manager
### Manages multiple .env files

To use
1. Check out code
2. set database variables in .env

## Commands
`env $name $for $value --delete --global`

updates and deletes variables for a specific configuration

`env:write $for --stdout`

writes configuration to either `app/files/$for.env` or to stdout if the flag is passed

`env:import $file $for --global`

imports settings from an existing .env file
