
import React from 'react';
import {BrowserRouter, Route, Routes} from "react-router-dom";
import Config from "./../App/Config";
import TasksPage from "../TasksPage/TasksPage";
import SettingsPage from "../SettingsPage/SettingsPage";
import './App.scss';
import Icon from "./Icon";
import HistoryPage from "../HistoryPage/HistoryPage";

const App = () => {
    const renderTasksPage = (title, icon, url = "", nested = true) => {
        let fetchFrom = Config.apiUrlPrefix + "/tasks" + url;
        return <TasksPage title={title} icon={icon} fetchFrom={fetchFrom} nested={nested}/>
    }
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/:root?/tasks/reminders" element={renderTasksPage("Reminders", <Icon name="bell"/>, "/reminders", false)} />
                <Route path="/:root?/tasks/todo" element={renderTasksPage("Todo", <Icon name="flash"/>, "/todo")} />
                <Route path="/:root?/tasks/status/progress" element={renderTasksPage("In Progress", <Icon name="flag"/>, "/status/progress", false)} />
                <Route path="/:root?/tasks/status/frozen" element={renderTasksPage("Frozen", <Icon name="certificate"/>, "/status/frozen")} />
                <Route path="/:root?/tasks/status/potential" element={renderTasksPage("Potential", <Icon name="calendar"/>, "/status/potential")} />
                <Route path="/:root?/tasks/status/cancelled" element={renderTasksPage("Cancelled", <Icon name="remove"/>, "/status/cancelled")} />
                <Route path="/:root?/tasks/status/completed" element={renderTasksPage("Completed", <Icon name="ok"/>, "/status/completed")} />
                <Route path="/:root?/tasks" element={renderTasksPage("All Tasks", <Icon name="list-alt"/>)} />
                <Route path="/settings" element={<SettingsPage/>} />
                <Route path="/:task?/history" element={<HistoryPage/>} />
            </Routes>
        </BrowserRouter>
    );
}

export default App;
