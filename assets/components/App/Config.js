const Config = new function () {
  return {
    apiUrlPrefix: "/internal-api",
    updateInputTimeout: 1200,
    updateSearchTimeout: 300,
    historyActionSpacerTime: 300,
    githubUrlPrefix: "https://github.com/",
    reminderTaskColor: 'rgb(255, 99, 71)',
    repeatedActionTypes: ["editTaskTitle", "editTaskDescription", "editTaskReminder"],
    repeatedActionMaxAmount: 4
  }
}

export default Config;
