# Import Umum
import math
import re
from os import path
from datetime import datetime
import mysql.connector

# Data Fetcher
import pandas as pd

# Numerical
import numpy as np

# Accuracy
from sklearn.metrics import accuracy_score, mean_squared_error, mean_absolute_error

# Scaler
from sklearn.preprocessing import MinMaxScaler

# Model, LSTM
from keras.models import Sequential, load_model
from keras.layers import Dense, LSTM
from keras.preprocessing.sequence import TimeseriesGenerator

# Graph plot
import matplotlib.pyplot as plt

# Get Variable
import sys

def main():
  namafile = sys.argv[1]
  df = pd.read_csv('c:/xampp/htdocs/SmartSys/public/dataset/' + namafile + '.csv', index_col='tanggal',parse_dates=True)
  df.index.freq='MS'
  
  percent = 80
  
  nilai_tengah = 80/100 * len(df)
  
  train = df.iloc[:round(nilai_tengah)]
  test = df.iloc[round(nilai_tengah):]
  cek_awal = df.iloc[:12]
  cek = df.iloc[12:]
  
  scaler = MinMaxScaler()
  
  scaler.fit(train)
  scaled_train = scaler.transform(train)
  scaled_test = scaler.transform(test)
  scaled_cek_awal = scaler.transform(cek_awal)
  scaled_cek = scaler.transform(cek)
  
  # define generator
  n_input = 3
  n_features = 1
  generator = TimeseriesGenerator(scaled_train, scaled_train, length=n_input, batch_size=1)
  
  X,y = generator[0]
  print(f'Given the Array: \n{X.flatten()}')
  print(f'Predict this y: \n {y}')
  
  # print(X.shape)
  
  # We do the same thing, but now instead for 12 months
  n_input = 12
  generator = TimeseriesGenerator(scaled_train, scaled_train, length=n_input, batch_size=1)
  
  # define model
  model = Sequential()
  model.add(LSTM(100, activation='relu', input_shape=(n_input, n_features)))
  model.add(Dense(1))
  model.compile(optimizer='adam', loss='mse')
  
  model.summary()
  
  # fit model
  model.fit(generator,epochs=50)
  
  last_train_batch = scaled_train[-12:]
  
  # print(last_train_batch)
  
  last_train_batch = last_train_batch.reshape((1, n_input, n_features))
  
  # print(model.predict(last_train_batch))
  
  # print(scaled_test[0])
  
  test_pembelians = []

  first_eval_batch = scaled_train[-n_input:]
  
  # print('awal', first_eval_batch)
  # print('asli', train[-n_input:])
  
  current_batch = first_eval_batch.reshape((1, n_input, n_features))
  
  # print('c', current_batch)

  for i in range(len(test)):
      
      # get the prediction value for the first batch
      current_pred = model.predict(current_batch)[0]
      
      # append the prediction into the array
      test_pembelians.append(current_pred) 
      
      # use the prediction to update the batch and remove the first value
      current_batch = np.append(current_batch[:,1:,:],[[current_pred]],axis=1)
  
  # print(test.head(),val.tail())
  
  # print(len(test_pembelians))
  
  true_pembelians = scaler.inverse_transform(test_pembelians)
  
  test['pembelians'] = true_pembelians
  
  print(len(true_pembelians))
  print(true_pembelians)
  print(len(test['pembelian']))
  
  # RMSE
  rmse = np.sqrt(mean_squared_error(test['pembelian'],test['pembelians']))
  print('Root mean square (RMSE) : ' + str(rmse))
  
  avg_y = np.mean(test['pembelian'])
  print('nilai rata2 data : ', avg_y)
  avg_p = np.mean(test['pembelians'])
  print('nilai rata2 hasil prakiraan : ', avg_p)
  per = ((avg_y - avg_p)/ avg_y) * 100
  if per < 0:
    akurasi = 100 + per
  else:
    akurasi = 100 - per
  print('nilai akurasi : ', akurasi)
  
  print('=============================')
  
  # print(len(cek[-12:]))
  
  cek_pembelians = []

  first_eval_batch = scaled_cek_awal[-n_input:]
  
  # print(first_eval_batch)
  # print(cek_awal[-n_input:])
  
  current_batch = first_eval_batch.reshape((1, n_input, n_features))

  for i in range(len(cek)):
      
      # get the prediction value for the first batch
      current_pred = model.predict(current_batch)[0]
      
      # append the prediction into the array
      cek_pembelians.append(current_pred) 
      
      # use the prediction to update the batch and remove the first value
      current_batch = np.append(current_batch[:,1:,:],[[current_pred]],axis=1)
  
  # print(cek.head(),val.tail())
  
  true_cek_pembelians = scaler.inverse_transform(cek_pembelians)
  
  cek['pembelians'] = true_cek_pembelians
  
  print(len(true_cek_pembelians))
  print(true_cek_pembelians)
  
  # RMSE
  rmse_cek = np.sqrt(mean_squared_error(cek['pembelian'],cek['pembelians']))
  print('Root mean square (RMSE) : ' + str(rmse_cek))
  
  avg_cek_y = np.mean(cek['pembelian'])
  print('nilai rata2 data : ', avg_cek_y)
  avg_cek_p = np.mean(cek['pembelians'])
  print('nilai rata2 hasil prakiraan : ', avg_cek_p)
  per = ((avg_cek_y - avg_cek_p)/ avg_cek_y) * 100
  
  if per < 0:
    akurasi = 100 + per
  else:
    akurasi = 100 - per
  print('nilai akurasi : ', akurasi)
  
  batasmin = int(sys.argv[2])
  if akurasi < batasmin:
    main()
  else:
    namajson = 'model_' + namafile + '.json'
    namamodel = 'model_' + namafile + '.h5'
    id = int(sys.argv[3])
    # DB
    mydb = mysql.connector.connect(
      host="localhost",
      user="root",
      password="",
      database="db_smartsys"
    )
    sql_cek = "SELECT nilai_akurasi FROM tb_model WHERE id_barang = %s"
    cursor = mydb.cursor()
    cursor.execute(sql_cek, (id,))
    nilaidb = cursor.fetchall()
    if nilaidb == []:
      model_json = model.to_json()
      with open('c:/xampp/htdocs/SmartSys/public/model/' + namajson, "w") as json_file:
          json_file.write(model_json)
      # serialize weights to HDF5
      model.save_weights('c:/xampp/htdocs/SmartSys/public/model/' + namamodel)
      # model.save('c:/xampp/htdocs/SmartSys/public/model/' + namamodel)
      sql_insert = "INSERT INTO tb_model (id_barang, nama_model, nilai_akurasi) VALUES (%s, %s, %s) "
      record_insert = (id, namamodel, akurasi)
      cursor = mydb.cursor()
      cursor.execute(sql_insert, record_insert)
      mydb.commit()
    else:
      if nilaidb < akurasi:
        model_json = model.to_json()
        with open('c:/xampp/htdocs/SmartSys/public/model/' + namajson, "w") as json_file:
            json_file.write(model_json)
        # serialize weights to HDF5
        model.save_weights('c:/xampp/htdocs/SmartSys/public/model/' + namamodel)
        # model.save('c:/xampp/htdocs/SmartSys/public/model/' + namamodel)
        sql_update = "UPDATE tb_model set nilai_akurasi = %s where namamodel = %s"
        input_data = (akurasi, namamodel)
        cursor = mydb.cursor()
        cursor.execute(sql_update, input_data)
        mydb.commit()
    # model_json = model.to_json()
    # with open("model_test.json", "w") as json_file:
    #     json_file.write(model_json)
    # # serialize weights to HDF5
    # model.save_weights("model_test.h5")
  

if __name__ == '__main__':
  main()