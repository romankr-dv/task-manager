import React from 'react';
import {BrowserRouter, Route, Routes} from "react-router-dom";
import Config from "./../App/Config";
import TasksPage from "../TasksPage/TasksPage";
import SettingsPage from "../SettingsPage/SettingsPage";
import './App.scss';
import Icon from "./Icon";
import HistoryPage from "../HistoryPage/HistoryPage";

const App = () => {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<AllTasks/>}/>
        <Route path="/:root?/tasks/reminders" element={<Reminders/>}/>
        <Route path="/:root?/tasks/todo" element={<Todo/>}/>
        <Route path="/:root?/tasks/status/progress" element={<InProgress/>}/>
        <Route path="/:root?/tasks/status/frozen" element={<Frozen/>}/>
        <Route path="/:root?/tasks/status/potential" element={<Potential/>}/>
        <Route path="/:root?/tasks/status/cancelled" element={<Cancelled/>}/>
        <Route path="/:root?/tasks/status/completed" element={<Completed/>}/>
        <Route path="/:root?/tasks" element={<AllTasks/>}/>
        <Route path="/settings" element={<SettingsPage/>}/>
        <Route path="/:task?/history" element={<HistoryPage/>}/>
      </Routes>
    </BrowserRouter>
  );
}

const Reminders = () => {
  let fetchFrom = Config.apiUrlPrefix + "/tasks/reminders";
  return <TasksPage title="Reminders" icon={<Icon name="bell"/>} fetchFrom={fetchFrom} nested={false}/>
}

const Todo = () => {
  let fetchFrom = Config.apiUrlPrefix + "/tasks/todo";
  return <TasksPage title="Todo" icon={<Icon name="flash"/>} fetchFrom={fetchFrom} nested={true}/>
}

const InProgress = () => {
  let fetchFrom = Config.apiUrlPrefix + "/tasks/status/progress";
  return <TasksPage title="In Progress" icon={<Icon name="flag"/>} fetchFrom={fetchFrom} nested={false}/>
}

const Frozen = () => {
  let fetchFrom = Config.apiUrlPrefix + "/tasks/status/frozen";
  return <TasksPage title="Frozen" icon={<Icon name="certificate"/>} fetchFrom={fetchFrom} nested={true}/>
}

const Potential = () => {
  let fetchFrom = Config.apiUrlPrefix + "/tasks/status/potential";
  return <TasksPage title="Potential" icon={<Icon name="calendar"/>} fetchFrom={fetchFrom} nested={true}/>
}

const Cancelled = () => {
  let fetchFrom = Config.apiUrlPrefix + "/tasks/status/cancelled";
  return <TasksPage title="Cancelled" icon={<Icon name="remove"/>} fetchFrom={fetchFrom} nested={true}/>
}

const Completed = () => {
  let fetchFrom = Config.apiUrlPrefix + "/tasks/status/completed";
  return <TasksPage title="Completed" icon={<Icon name="ok"/>} fetchFrom={fetchFrom} nested={true}/>
}

const AllTasks = () => {
  let fetchFrom = Config.apiUrlPrefix + "/tasks";
  return <TasksPage title="All Tasks" icon={<Icon name="list-alt"/>} fetchFrom={fetchFrom} nested={true}/>
}

export default App;
